<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vehicle extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'seats', 'luggage', 'transmission', 'fuel', 'price_per_day',
        'driver_charge_per_day', 'self_drive', 'with_driver', 'services', 'features', 'images',
        'description', 'is_featured', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'driver_charge_per_day' => 'decimal:2',
            'self_drive' => 'boolean',
            'with_driver' => 'boolean',
            'services' => 'array',
            'features' => 'array',
            'images' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(ServiceBooking::class, 'bookable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getMainImageAttribute(): string
    {
        return media_url($this->images[0] ?? null);
    }

    public function getImageUrlsAttribute(): array
    {
        return array_map(fn ($image) => media_url($image), $this->images ?: [null]);
    }

    public function getCategoryLabelAttribute(): string
    {
        return config('travel.vehicle_categories.'.$this->category, ucfirst($this->category));
    }

    /** Dates already taken by confirmed / pending bookings, for the availability hint. */
    public function isBookedBetween(string $from, string $to): bool
    {
        return $this->bookings()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->where('travel_date', '<=', $to)
            ->where(fn ($q) => $q->where('return_date', '>=', $from)->orWhereNull('return_date'))
            ->exists();
    }
}
