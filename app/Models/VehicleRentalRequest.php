<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleRentalRequest extends Model
{
    protected $fillable = [
        'user_id',
        'rental_partner_id',
        'vehicle_type',
        'pickup_location',
        'dropoff_location',
        'pickup_date',
        'pickup_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
        ];
    }

    public function traveller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rentalPartner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rental_partner_id');
    }
}
