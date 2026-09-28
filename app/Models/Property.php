<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_id',
        'name',
        'slug',
        'property_type',
        'type',
        'status',
        'city',
        'country',
        'address',
        'description',
        'price_per_night',
        'currency',
        'rating',
        'review_count',
        'stars',
        'images',
        'amenities',
        'max_guests',
        'bedrooms',
        'is_featured',
        'home_section',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
            'rating' => 'decimal:1',
            'images' => 'array',
            'amenities' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->where('home_section', 'popular');
    }

    public function scopeTrending(Builder $query): Builder
    {
        return $query->where('home_section', 'trending');
    }

    /**
     * Manually positioned packages (set by a super admin) come first, in position order;
     * everything else falls back to newest-first.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('position IS NULL')->orderBy('position')->orderByDesc('created_at');
    }

    public function getMainImageAttribute(): string
    {
        $image = $this->images[0] ?? null;

        if (! $image) {
            return asset('images/placeholder.svg');
        }

        return str_starts_with($image, 'http') ? $image : Storage::disk('public')->url($image);
    }

    public function getRatingLabelAttribute(): string
    {
        return match (true) {
            $this->rating >= 9.0 => 'Exceptional',
            $this->rating >= 8.0 => 'Excellent',
            $this->rating >= 7.0 => 'Very Good',
            $this->rating >= 6.0 => 'Good',
            default => 'Fair',
        };
    }
}
