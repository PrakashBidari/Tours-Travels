<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewVendorRegisteredNotification;
use Illuminate\Auth\Events\Verified;

class NotifySuperAdminOfNewVendor
{
    public function handle(Verified $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || (! $user->isVendor() && ! $user->isRentalPartner())) {
            return;
        }

        User::where('role', 'super_admin')->get()
            ->each(fn (User $admin) => $admin->notify(new NewVendorRegisteredNotification($user)));
    }
}
