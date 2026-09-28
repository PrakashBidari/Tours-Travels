<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RentalPartnerApprovedNotification;
use App\Notifications\RentalPartnerRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RentalPartnerController extends Controller
{
    use PasswordValidationRules;

    public function index(Request $request): View
    {
        $rentalPartners = User::where('role', 'rental_partner')
            ->when($request->filled('status'), fn ($q) => $q->where('vendor_status', $request->string('status')))
            ->latest()
            ->get();

        return view('backend.rental-partners.index', ['rentalPartners' => $rentalPartners]);
    }

    public function create(): View
    {
        return view('backend.rental-partners.form');
    }

    /**
     * Only a super admin can reach this (route is gated by role:super_admin). Requires the
     * exact same details a rental partner gives on public self-registration (name, email, password,
     * company name/phone/address) — without them the account is rejected. Admin-created
     * rental partners are auto-approved since the admin creating the account is itself the review step.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'company_name' => ['required', 'string', 'max:255'],
            'company_phone' => ['required', 'string', 'max:50'],
            'company_address' => ['required', 'string', 'max:255'],
            'company_description' => ['nullable', 'string', 'max:2000'],
            'company_logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = $request->hasFile('company_logo')
            ? $request->file('company_logo')->store('rental-partner-logos', 'public')
            : null;

        $rentalPartner = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'rental_partner',
            'vendor_status' => 'approved',
            'company_name' => $data['company_name'],
            'company_phone' => $data['company_phone'],
            'company_address' => $data['company_address'],
            'company_description' => $data['company_description'] ?? null,
            'company_logo' => $logoPath,
        ]);

        // email_verified_at isn't mass-assignable (see User::$fillable), so it must be set
        // this way — an admin-created account needs no separate confirmation step.
        $rentalPartner->markEmailAsVerified();

        return redirect()->route('dashboard.rental-partners.index')->with('status', "\"{$data['company_name']}\" has been created and approved.");
    }

    public function approve(User $rentalPartner): RedirectResponse
    {
        $rentalPartner->update(['vendor_status' => 'approved']);
        $rentalPartner->notify(new RentalPartnerApprovedNotification);

        return back()->with('status', "{$rentalPartner->name} has been approved as a rental partner.");
    }

    public function reject(Request $request, User $rentalPartner): RedirectResponse
    {
        $reason = $request->string('reason')->value() ?: null;

        $rentalPartner->update(['vendor_status' => 'rejected']);
        $rentalPartner->notify(new RentalPartnerRejectedNotification($reason));

        return back()->with('status', "{$rentalPartner->name}'s rental partner application has been rejected.");
    }
}
