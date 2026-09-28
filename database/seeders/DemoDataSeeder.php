<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Property;
use App\Models\User;
use Database\Factories\DestinationFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Seeds 5 fully-detailed, approved vendors — each with 5 destinations, and each
     * destination with 10 packages — plus 10 plain traveler accounts, so every vendor
     * → destination → package flow has real data to click through. Never touches the
     * super_admin account (see SuperAdminSeeder). Idempotent: re-running this clears out
     * and rebuilds only the destinations/packages owned by these specific demo vendors.
     */
    protected static array $vendors = [
        ['company' => 'Alpine Horizon Travels', 'city' => 'Zurich', 'country' => 'Switzerland'],
        ['company' => 'Golden Sands Tours', 'city' => 'Dubai', 'country' => 'United Arab Emirates'],
        ['company' => 'Emerald Coast Voyages', 'city' => 'Sydney', 'country' => 'Australia'],
        ['company' => 'Silk Route Adventures', 'city' => 'Istanbul', 'country' => 'Turkey'],
        ['company' => 'Maple Leaf Journeys', 'city' => 'Toronto', 'country' => 'Canada'],
    ];

    public function run(): void
    {
        foreach (static::$vendors as $index => $profile) {
            $n = $index + 1;

            $vendor = User::updateOrCreate(
                ['email' => "vendor{$n}@example.com"],
                [
                    'name' => fake()->name(),
                    'password' => Hash::make('password'),
                    'role' => 'vendor',
                    'vendor_status' => 'approved',
                    'phone' => fake()->phoneNumber(),
                    'address' => fake()->streetAddress(),
                    'city' => $profile['city'],
                    'country' => $profile['country'],
                    'postal_code' => fake()->postcode(),
                    'company_name' => $profile['company'],
                    'company_phone' => fake()->phoneNumber(),
                    'company_address' => fake()->streetAddress().', '.$profile['city'],
                    'company_description' => "{$profile['company']} is a licensed tour operator based in {$profile['city']}, {$profile['country']}, curating unforgettable travel experiences for guests worldwide.",
                    'commission_rate' => 15.00,
                    'email_verified_at' => now(),
                ]
            );

            // Rebuild this vendor's catalogue from scratch so re-seeding doesn't pile up duplicates.
            Property::where('user_id', $vendor->id)->delete();
            Destination::where('user_id', $vendor->id)->delete();

            $places = collect(DestinationFactory::places())->shuffle()->take(5);

            foreach ($places as $place) {
                $destination = Destination::factory()
                    ->forPlace($place['city'], $place['country'])
                    ->create([
                        'user_id' => $vendor->id,
                        'status' => 'approved',
                    ]);

                Property::factory()
                    ->count(10)
                    ->forDestination($place['city'], $place['country'])
                    ->create([
                        'user_id' => $vendor->id,
                        'destination_id' => $destination->id,
                        'status' => 'approved',
                    ]);
            }
        }

        for ($n = 1; $n <= 10; $n++) {
            User::updateOrCreate(
                ['email' => "traveler{$n}@example.com"],
                [
                    'name' => fake()->name(),
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'vendor_status' => null,
                    'phone' => fake()->phoneNumber(),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
