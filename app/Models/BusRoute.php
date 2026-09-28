<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class BusRoute extends Model
{
    public const TYPES = [
        'tourist' => 'Tourist Bus',
        'deluxe' => 'Deluxe',
        'sofa' => 'Luxury Sofa Bus',
        'vip' => 'VIP',
        'night' => 'Night Bus',
        'ac' => 'AC Deluxe',
    ];

    protected $fillable = [
        'operator', 'bus_name', 'bus_type', 'from_city', 'to_city', 'departure_time', 'arrival_time',
        'boarding_point', 'dropping_point', 'price', 'total_seats', 'amenities', 'image', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'amenities' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function seatReservations(): HasMany
    {
        return $this->hasMany(BusSeatReservation::class);
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(ServiceBooking::class, 'bookable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->bus_type] ?? ucfirst($this->bus_type);
    }

    public function getImageUrlAttribute(): string
    {
        return media_url($this->image, config('travel.images.bus'));
    }

    public function getDepartureLabelAttribute(): string
    {
        return Carbon::parse($this->departure_time)->format('g:i A');
    }

    public function getArrivalLabelAttribute(): string
    {
        return Carbon::parse($this->arrival_time)->format('g:i A');
    }

    /** Travel time, wrapping past midnight for night buses. */
    public function getDurationLabelAttribute(): string
    {
        $departure = Carbon::parse($this->departure_time);
        $arrival = Carbon::parse($this->arrival_time);

        if ($arrival->lessThanOrEqualTo($departure)) {
            $arrival->addDay();
        }

        $minutes = $departure->diffInMinutes($arrival);

        return intdiv($minutes, 60).'h '.str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT).'m';
    }

    /**
     * Seat map as rows of seat labels in a 2 + 2 layout (A/B window-aisle on the
     * left, C/D on the right); the final row is a 5-seat bench when seats allow.
     *
     * @return array<int, array<int, string|null>>  null = aisle gap
     */
    public function seatRows(): array
    {
        $rows = [];
        $remaining = $this->total_seats;
        $row = 1;

        while ($remaining > 0) {
            if ($remaining === 5) {
                $rows[] = array_map(fn ($letter) => $row.$letter, ['A', 'B', 'E', 'C', 'D']);
                break;
            }

            $count = min(4, $remaining);
            $letters = array_slice(['A', 'B', 'C', 'D'], 0, $count);
            $seats = array_map(fn ($letter) => $row.$letter, $letters);
            $rows[] = array_merge(array_slice($seats, 0, 2), [null], array_slice($seats, 2));

            $remaining -= $count;
            $row++;
        }

        return $rows;
    }

    public function allSeatLabels(): array
    {
        return array_values(array_filter(array_merge(...$this->seatRows())));
    }

    /** @return array<int, string> */
    public function bookedSeats(string $date): array
    {
        return $this->seatReservations()->whereDate('travel_date', $date)->pluck('seat_number')->all();
    }

    public function availableSeatCount(string $date): int
    {
        return $this->total_seats - count($this->bookedSeats($date));
    }
}
