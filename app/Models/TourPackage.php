<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TourPackage extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'destination', 'country', 'duration_days', 'duration_nights',
        'price', 'sale_price', 'trip_style', 'season', 'difficulty', 'max_altitude', 'group_size',
        'hotel', 'meals', 'transport', 'summary', 'overview', 'highlights', 'itinerary', 'includes',
        'excludes', 'visa_info', 'map_embed_url', 'images', 'rating', 'review_count', 'is_featured',
        'is_active', 'position', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'rating' => 'decimal:1',
            'highlights' => 'array',
            'itinerary' => 'array',
            'includes' => 'array',
            'excludes' => 'array',
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

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('position IS NULL')->orderBy('position')->orderByDesc('is_featured')->orderByDesc('created_at');
    }

    /** The price a customer actually pays per person. */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->sale_price && $this->sale_price < $this->price ? $this->sale_price : $this->price);
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->sale_price || $this->sale_price >= $this->price) {
            return null;
        }

        return (int) round(100 - ($this->sale_price / $this->price * 100));
    }

    public function getMainImageAttribute(): string
    {
        return media_url($this->images[0] ?? null);
    }

    public function getImageUrlsAttribute(): array
    {
        return array_map(fn ($image) => media_url($image), $this->images ?: [null]);
    }

    public function getDurationLabelAttribute(): string
    {
        $label = $this->duration_days.' '.str('Day')->plural($this->duration_days);

        return $this->duration_nights
            ? $label.' / '.$this->duration_nights.' '.str('Night')->plural($this->duration_nights)
            : $label;
    }

    public function getCategoryLabelAttribute(): string
    {
        return config('travel.tour_categories.'.$this->category, ucfirst($this->category));
    }

    public function getSeasonLabelAttribute(): string
    {
        return config('travel.seasons.'.$this->season, ucfirst((string) $this->season));
    }
}
