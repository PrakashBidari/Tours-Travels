<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Destination>
 */
class DestinationFactory extends Factory
{
    protected static array $places = [
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
        ['city' => 'Marrakesh', 'country' => 'Morocco'],
        ['city' => 'Reykjavik', 'country' => 'Iceland'],
        ['city' => 'Santorini', 'country' => 'Greece'],
        ['city' => 'Zurich', 'country' => 'Switzerland'],
        ['city' => 'Toronto', 'country' => 'Canada'],
        ['city' => 'Seoul', 'country' => 'South Korea'],
        ['city' => 'Cairo', 'country' => 'Egypt'],
        ['city' => 'Buenos Aires', 'country' => 'Argentina'],
        ['city' => 'Queenstown', 'country' => 'New Zealand'],
        ['city' => 'Edinburgh', 'country' => 'United Kingdom'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $place = fake()->randomElement(static::$places);

        return array_merge(
            ['user_id' => User::factory()->state(['role' => 'vendor', 'vendor_status' => 'approved'])],
            $this->placeAttributes($place['city'], $place['country']),
            [
                'description' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
                'status' => 'approved',
                'is_featured' => fake()->boolean(20),
            ]
        );
    }

    /**
     * The full city/country pool, exposed for callers (e.g. demo seeders) that want to pick
     * specific places themselves rather than let the factory pick randomly.
     *
     * @return array<int, array{city: string, country: string}>
     */
    public static function places(): array
    {
        return static::$places;
    }

    /**
     * Pin this destination to a specific city/country, keeping name, slug and image seed
     * consistent with each other (used when the caller — e.g. a demo seeder — needs a
     * destination for a known place rather than a random one from the pool).
     */
    public function forPlace(string $city, string $country): static
    {
        return $this->state(fn () => $this->placeAttributes($city, $country));
    }

    protected function placeAttributes(string $city, string $country): array
    {
        $seed = Str::slug($city).'-'.fake()->unique()->numberBetween(1, 1000000);

        return [
            'name' => $city,
            'slug' => Str::slug($city).'-'.fake()->unique()->numberBetween(1000, 999999),
            'city' => $city,
            'country' => $country,
            'image' => "https://picsum.photos/seed/{$seed}/900/600",
        ];
    }
}
