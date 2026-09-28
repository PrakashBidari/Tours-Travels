<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VendorApprovedNotification;
use App\Notifications\VendorRejectedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class VendorController extends Controller
{
    use PasswordValidationRules;

    public function index(Request $request): View
    {
        $vendors = User::where('role', 'vendor')
            ->withCount('properties')
            ->when($request->filled('status'), fn ($q) => $q->where('vendor_status', $request->string('status')))
            ->latest()
            ->get();

        return view('backend.vendors.index', ['vendors' => $vendors]);
    }

    public function create(): View
    {
        return view('backend.vendors.form');
    }

    /**
     * Only a super admin can reach this (route is gated by role:super_admin). Requires the
     * exact same details a vendor gives on public self-registration (name, email, password,
     * company name/phone/address) — without them the account is rejected. Admin-created
     * vendors are auto-approved since the admin creating the account is itself the review step.
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
            ? $request->file('company_logo')->store('vendor-logos', 'public')
            : null;

        $vendor = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'vendor',
            'vendor_status' => 'approved',
            'company_name' => $data['company_name'],
            'company_phone' => $data['company_phone'],
            'company_address' => $data['company_address'],
            'company_description' => $data['company_description'] ?? null,
            'company_logo' => $logoPath,
        ]);

        // email_verified_at isn't mass-assignable (see User::$fillable), so it must be set
        // this way — an admin-created account needs no separate confirmation step.
        $vendor->markEmailAsVerified();

        return redirect()->route('dashboard.vendors.index')->with('status', "\"{$data['company_name']}\" has been created and approved.");
    }

    public function approve(User $vendor): RedirectResponse
    {
        $vendor->update(['vendor_status' => 'approved']);
        $vendor->notify(new VendorApprovedNotification);

        return back()->with('status', "{$vendor->name} has been approved as a vendor.");
    }

    public function reject(Request $request, User $vendor): RedirectResponse
    {
        $reason = $request->string('reason')->value() ?: null;

        $vendor->update(['vendor_status' => 'rejected']);
        $vendor->notify(new VendorRejectedNotification($reason));

        return back()->with('status', "{$vendor->name}'s vendor application has been rejected.");
    }
}
