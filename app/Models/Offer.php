<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** A promotional deal shown on the Offers page; with a `code` it doubles as a coupon. */
class Offer extends Model
{
    protected $fillable = [
        'title', 'code', 'description', 'discount_type', 'discount_value', 'min_amount', 'max_discount',
        'applies_to', 'badge', 'image', 'link_url', 'starts_at', 'expires_at', 'usage_limit', 'used_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function getImageUrlAttribute(): string
    {
        return media_url($this->image, config('travel.images.beach'));
    }

    public function getDiscountLabelAttribute(): string
    {
        return $this->discount_type === 'percent'
            ? rtrim(rtrim(number_format((float) $this->discount_value, 2), '0'), '.').'% OFF'
            : npr($this->discount_value).' OFF';
    }

    /**
     * Validate this coupon for a cart of `$amount` on `$serviceType` and return the
     * discount in NPR, or an error message string when it cannot be applied.
     */
    public function discountFor(float $amount, string $serviceType): float|string
    {
        return match (true) {
            ! $this->is_active => 'This coupon is no longer active.',
            $this->starts_at && $this->starts_at->isFuture() => 'This coupon is not valid yet.',
            $this->expires_at && $this->expires_at->isPast() => 'This coupon has expired.',
            $this->usage_limit !== null && $this->used_count >= $this->usage_limit => 'This coupon has reached its usage limit.',
            $this->applies_to !== 'all' && $this->applies_to !== $serviceType => 'This coupon does not apply to this service.',
            $amount < (float) $this->min_amount => 'Minimum booking amount for this coupon is '.npr($this->min_amount).'.',
            default => $this->calculate($amount),
        };
    }

    protected function calculate(float $amount): float
    {
        $discount = $this->discount_type === 'percent'
            ? $amount * (float) $this->discount_value / 100
            : (float) $this->discount_value;

        if ($this->max_discount !== null) {
            $discount = min($discount, (float) $this->max_discount);
        }

        return round(min($discount, $amount), 2);
    }
}
