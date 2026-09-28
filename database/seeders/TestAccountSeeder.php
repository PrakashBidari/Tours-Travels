<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountSeeder extends Seeder
{
    /**
     * Seed one vendor and one traveler account for manual testing.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name' => 'Test Vendor',
                'password' => Hash::make('password'),
                'role' => 'vendor',
                'vendor_status' => 'approved',
                'company_name' => 'Test Vendor Co',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Test Traveler',
                'password' => Hash::make('password'),
                'role' => 'user',
                'vendor_status' => null,
                'email_verified_at' => now(),
            ]
        );
    }
}
