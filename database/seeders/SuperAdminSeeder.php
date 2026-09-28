<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the single Super Admin account. Idempotent: re-running this
     * seeder only ever updates the one super_admin row, never creates a second.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'admin@booking.test')],
            [
                'name' => env('SUPER_ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'ChangeMe123!')),
                'role' => 'super_admin',
                'vendor_status' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
