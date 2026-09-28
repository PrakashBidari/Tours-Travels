<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ConfirmRegistrationNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Jetstream\Jetstream;

class PendingRegistrationController extends Controller
{
    use PasswordValidationRules;

    /** Cache key prefix for pending (unverified) registrations. */
    private const CACHE_PREFIX = 'pending_registration:';

    public function create(): View
    {
        return view('frontend.auth.register');
    }

    /**
     * Validate the submitted registration and email a confirmation link.
     * No User row is created here — the account is only persisted once the
     * link is clicked, see confirm().
     */
    public function store(Request $request): RedirectResponse
    {
        $input = $request->all();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'role' => ['required', 'in:user,vendor,rental_partner'],
            'company_name' => ['required_if:role,vendor,rental_partner', 'nullable', 'string', 'max:255'],
            'company_phone' => ['required_if:role,vendor,rental_partner', 'nullable', 'string', 'max:50'],
            'company_address' => ['required_if:role,vendor,rental_partner', 'nullable', 'string', 'max:255'],
            'company_description' => ['nullable', 'string', 'max:2000'],
            'company_logo' => ['nullable', 'image', 'max:2048'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $needsBusinessDetails = in_array($input['role'], ['vendor', 'rental_partner'], true);

        $logoPath = $needsBusinessDetails && ! empty($input['company_logo'])
            ? $input['company_logo']->store('vendor-logos/pending', 'public')
            : null;

        $token = Str::random(64);

        Cache::put(self::CACHE_PREFIX.$token, [
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'role' => $input['role'],
            'company_name' => $needsBusinessDetails ? $input['company_name'] : null,
            'company_phone' => $needsBusinessDetails ? ($input['company_phone'] ?? null) : null,
            'company_address' => $needsBusinessDetails ? ($input['company_address'] ?? null) : null,
            'company_description' => $needsBusinessDetails ? ($input['company_description'] ?? null) : null,
            'company_logo' => $logoPath,
        ], now()->addHour());

        $verificationUrl = URL::temporarySignedRoute(
            'register.confirm',
            now()->addHour(),
            ['token' => $token]
        );

        Notification::route('mail', $input['email'])
            ->notify(new ConfirmRegistrationNotification($input['name'], $verificationUrl));

        return redirect()->route('login')->with(
            'status',
            "We've sent a confirmation link to {$input['email']}. Click it to activate your account — it expires in 60 minutes."
        );
    }

    /**
     * Signed link target. Creates the User for the first time here, already
     * verified, then logs them in.
     */
    public function confirm(Request $request, string $token): RedirectResponse
    {
        $data = Cache::get(self::CACHE_PREFIX.$token);

        if (! $data) {
            return redirect()->route('register')->withErrors([
                'email' => 'This confirmation link is invalid or has expired. Please register again.',
            ]);
        }

        Cache::forget(self::CACHE_PREFIX.$token);

        $needsBusinessDetails = in_array($data['role'], ['vendor', 'rental_partner'], true);

        $logoPath = null;
        if ($data['company_logo']) {
            $logoPath = Str::replaceFirst('vendor-logos/pending/', 'vendor-logos/', $data['company_logo']);
            Storage::disk('public')->move($data['company_logo'], $logoPath);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'vendor_status' => $needsBusinessDetails ? 'pending' : null,
            'company_name' => $data['company_name'],
            'company_phone' => $data['company_phone'],
            'company_address' => $data['company_address'],
            'company_description' => $data['company_description'],
            'company_logo' => $logoPath,
        ]);

        $user->markEmailAsVerified();

        event(new Verified($user));

        Auth::login($user);

        return redirect()->intended($user->dashboardUrl())
            ->with('status', 'Your email is confirmed — welcome aboard!');
    }
}
