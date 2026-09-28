<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

/**
 * A booking for any Ram Tours service other than the legacy hotel/property flow:
 * tour package, flight ticket, bus seat(s), car rental or visa application.
 */
class ServiceBooking extends Model
{
    protected $fillable = [
        'reference', 'user_id', 'service_type', 'bookable_type', 'bookable_id', 'title', 'full_name',
        'email', 'phone', 'nationality', 'passport_number', 'travel_date', 'return_date', 'adults',
        'children', 'special_request', 'details', 'unit_price', 'quantity', 'subtotal', 'discount',
        'total', 'currency', 'coupon_code', 'payment_method', 'payment_status', 'transaction_id',
        'paid_at', 'status', 'pnr', 'ticket_number', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'return_date' => 'date',
            'details' => 'array',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ServiceBooking $booking) {
            $booking->reference ??= static::generateReference();
        });
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'RT'.now()->format('ymd').strtoupper(Str::random(4));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function seatReservations(): HasMany
    {
        return $this->hasMany(BusSeatReservation::class);
    }

    public function getServiceLabelAttribute(): string
    {
        return config('travel.service_types.'.$this->service_type, ucfirst($this->service_type));
    }

    public function getPaymentMethodLabelAttribute(): ?string
    {
        return $this->payment_method
            ? config('travel.payment_methods.'.$this->payment_method.'.label', ucfirst($this->payment_method))
            : null;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /** A booking can be paid online while it is still open and has an amount due. */
    public function isPayable(): bool
    {
        return ! $this->isPaid()
            && $this->total > 0
            && ! in_array($this->status, ['cancelled', 'refunded'], true);
    }

    public function markPaid(string $method, ?string $transactionId = null): void
    {
        $this->update([
            'payment_method' => $method,
            'payment_status' => 'paid',
            'transaction_id' => $transactionId,
            'paid_at' => now(),
            'status' => $this->status === 'pending' ? 'confirmed' : $this->status,
        ]);
    }

    /** Tailwind classes for a status pill. */
    public static function badgeClasses(string $status): string
    {
        return match ($status) {
            'confirmed', 'paid', 'ticketed', 'completed', 'resolved', 'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'pending', 'processing', 'unpaid', 'new', 'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            'cancelled', 'failed', 'rejected' => 'bg-red-50 text-red-700 ring-red-600/20',
            'refunded' => 'bg-slate-100 text-slate-700 ring-slate-500/20',
            default => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        };
    }
}
