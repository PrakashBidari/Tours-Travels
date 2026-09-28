<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VisaService extends Model
{
    protected $fillable = [
        'country', 'flag', 'visa_type', 'title', 'slug', 'processing_time', 'validity', 'stay_duration',
        'embassy_fee', 'service_charge', 'requirements', 'description', 'image', 'is_featured', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'embassy_fee' => 'decimal:2',
            'service_charge' => 'decimal:2',
            'requirements' => 'array',
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

    public function getTotalFeeAttribute(): float
    {
        return (float) $this->embassy_fee + (float) $this->service_charge;
    }

    public function getTypeLabelAttribute(): string
    {
        return config('travel.visa_types.'.$this->visa_type, ucfirst($this->visa_type));
    }

    public function getImageUrlAttribute(): string
    {
        return media_url($this->image, config('travel.images.visa'));
    }
}
