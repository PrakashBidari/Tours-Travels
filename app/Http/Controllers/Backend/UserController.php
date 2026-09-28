<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    use PasswordValidationRules;

    public function index(): View
    {
        $users = User::where('role', 'user')
            ->withCount('bookings')
            ->latest()
            ->get();

        return view('backend.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        return view('backend.users.form');
    }

    /**
     * Only a super admin can reach this (route is gated by role:super_admin). Traveler
     * accounts created here need the same fields as public self-registration.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'vendor_status' => null,
        ]);

        // email_verified_at isn't mass-assignable (see User::$fillable), so it must be set
        // this way — an admin-created account needs no separate confirmation step.
        $user->markEmailAsVerified();

        return redirect()->route('dashboard.users.index')->with('status', "\"{$data['name']}\" has been created.");
    }
}
