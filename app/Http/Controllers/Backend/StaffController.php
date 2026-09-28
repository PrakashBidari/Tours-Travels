<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Staff accounts (Manager, Ticketing, Consultant, Content Editor, Finance). Super admin only. */
class StaffController extends Controller
{
    use PasswordValidationRules;

    public function index(): View
    {
        return view('backend.staff.index', [
            'staff' => User::whereIn('role', ['super_admin', 'staff'])->orderBy('role')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('backend.staff.form', ['member' => new User(['staff_role' => 'consultant'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:25'],
            'staff_role' => ['required', Rule::in(array_keys(config('travel.staff_roles')))],
            'password' => $this->passwordRules(),
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'staff',
            'staff_role' => $data['staff_role'],
        ]);

        $user->markEmailAsVerified();

        return redirect()->route('dashboard.staff.index')->with('status', "{$user->name} can now sign in as {$user->staff_role_label}.");
    }

    public function edit(User $staff): View
    {
        abort_unless($staff->isStaff(), 404);

        return view('backend.staff.form', ['member' => $staff]);
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        abort_unless($staff->isStaff(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'staff_role' => ['required', Rule::in(array_keys(config('travel.staff_roles')))],
            'password' => ['nullable', ...array_filter($this->passwordRules(), fn ($rule) => $rule !== 'required')],
        ]);

        $staff->fill(['name' => $data['name'], 'phone' => $data['phone'] ?? null, 'staff_role' => $data['staff_role']]);

        if (! empty($data['password'])) {
            $staff->password = Hash::make($data['password']);
        }

        $staff->save();

        return redirect()->route('dashboard.staff.index')->with('status', "{$staff->name} updated.");
    }

    public function destroy(User $staff): RedirectResponse
    {
        abort_unless($staff->isStaff(), 404);

        $staff->delete();

        return back()->with('status', "{$staff->name}'s staff account was removed.");
    }
}
