<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected static array $cities = [
        ['city' => 'Paris', 'country' => 'France'],
        ['city' => 'London', 'country' => 'United Kingdom'],
        ['city' => 'New York', 'country' => 'United States'],
        ['city' => 'Tokyo', 'country' => 'Japan'],
        ['city' => 'Dubai', 'country' => 'United Arab Emirates'],
        ['city' => 'Rome', 'country' => 'Italy'],
        ['city' => 'Barcelona', 'country' => 'Spain'],
        ['city' => 'Bangkok', 'country' => 'Thailand'],
        ['city' => 'Singapore', 'country' => 'Singapore'],
        ['city' => 'Sydney', 'country' => 'Australia'],
        ['city' => 'Amsterdam', 'country' => 'Netherlands'],
        ['city' => 'Istanbul', 'country' => 'Turkey'],
        ['city' => 'Prague', 'country' => 'Czech Republic'],
        ['city' => 'Lisbon', 'country' => 'Portugal'],
        ['city' => 'Bali', 'country' => 'Indonesia'],
        ['city' => 'Cape Town', 'country' => 'South Africa'],
        ['city' => 'Rio de Janeiro', 'country' => 'Brazil'],
        ['city' => 'Vancouver', 'country' => 'Canada'],
        ['city' => 'Vienna', 'country' => 'Austria'],
        ['city' => 'Kyoto', 'country' => 'Japan'],
    ];

    protected static array $propertyTypes = [
        'hotel', 'hotel', 'hotel', 'apartment', 'apartment', 'resort', 'villa', 'guest_house',
    ];

    protected static array $namePatterns = [
        'hotel' => ['The Grand {city} Hotel', '{city} Central Hotel', 'Hotel {city} Plaza', 'The {city} Royal'],
        'apartment' => ['{city} Riverside Apartments', '{city} City Loft', 'Modern {city} Studio', '{city} Suites'],
        'resort' => ['{city} Beach Resort', 'The {city} Resort & Spa', '{city} Paradise Resort'],
        'villa' => ['{city} Garden Villa', 'Villa {city}', '{city} Hillside Villa'],
        'guest_house' => ['{city} Cozy Guest House', '{city} Bed & Breakfast', 'The {city} Inn'],
    ];

    protected static array $amenityPool = [
        'Free WiFi', 'Free parking', 'Swimming pool', 'Air conditioning', 'Breakfast included',
        'Pet friendly', 'Fitness center', 'Spa', 'Bar', 'Restaurant', 'Non-smoking rooms',
        'Airport shuttle', '24-hour front desk', 'Room service', 'Family rooms', 'Terrace',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $location = fake()->randomElement(static::$cities);
        $type = fake()->randomElement(static::$propertyTypes);
        $namePattern = fake()->randomElement(static::$namePatterns[$type]);
        $name = str_replace('{city}', $location['city'], $namePattern);

        $seed = Str::slug($name).'-'.fake()->unique()->numberBetween(1, 100000);

        return [
            'user_id' => User::factory()->state(['role' => 'vendor', 'vendor_status' => 'approved']),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 9999),
            'property_type' => $type,
            'city' => $location['city'],
            'country' => $location['country'],
            'address' => fake()->streetAddress(),
            'description' => fake()->paragraphs(3, true),
            'price_per_night' => fake()->numberBetween(40, 450),
            'currency' => 'USD',
            'rating' => fake()->randomFloat(1, 6.0, 9.9),
            'review_count' => fake()->numberBetween(10, 2400),
            'stars' => in_array($type, ['hotel', 'resort']) ? fake()->numberBetween(2, 5) : null,
            'images' => collect(range(1, 4))
                ->map(fn ($i) => "https://picsum.photos/seed/{$seed}-{$i}/900/600")
                ->all(),
            'amenities' => fake()->randomElements(static::$amenityPool, fake()->numberBetween(4, 8)),
            'max_guests' => fake()->numberBetween(1, 6),
            'bedrooms' => in_array($type, ['apartment', 'villa']) ? fake()->numberBetween(1, 4) : null,
            'is_featured' => fake()->boolean(10),
        ];
    }

    /**
     * Pin this package to a specific city/country, regenerating the name, slug, type-dependent
     * fields and image seed so they stay consistent with each other (used when the caller — e.g.
     * a demo seeder — needs a package that matches a known destination rather than a random city).
     */
    public function forDestination(string $city, string $country): static
    {
        return $this->state(function () use ($city, $country) {
            $type = fake()->randomElement(static::$propertyTypes);
            $namePattern = fake()->randomElement(static::$namePatterns[$type]);
            $name = str_replace('{city}', $city, $namePattern);
            $seed = Str::slug($name).'-'.fake()->unique()->numberBetween(1, 1000000);

            return [
                'name' => $name,
                'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1000, 999999),
                'property_type' => $type,
                'city' => $city,
                'country' => $country,
                'stars' => in_array($type, ['hotel', 'resort']) ? fake()->numberBetween(2, 5) : null,
                'bedrooms' => in_array($type, ['apartment', 'villa']) ? fake()->numberBetween(1, 4) : null,
                'images' => collect(range(1, 4))
                    ->map(fn ($i) => "https://picsum.photos/seed/{$seed}-{$i}/900/600")
                    ->all(),
            ];
        });
    }
}
