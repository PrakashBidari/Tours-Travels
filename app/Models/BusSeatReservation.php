<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusSeatReservation extends Model
{
    protected $fillable = ['bus_route_id', 'service_booking_id', 'travel_date', 'seat_number'];

    protected function casts(): array
    {
        return ['travel_date' => 'date'];
    }

    public function busRoute(): BelongsTo
    {
        return $this->belongsTo(BusRoute::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ServiceBooking::class, 'service_booking_id');
    }
}
