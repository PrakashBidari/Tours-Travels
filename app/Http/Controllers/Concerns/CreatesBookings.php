<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use App\Notifications\BookingReceivedNotification;
use App\Notifications\NewBookingNotification;
use Illuminate\Support\Carbon;

trait CreatesBookings
{
    protected function createBooking(User $user, Property $property, string|Carbon $checkIn, string|Carbon $checkOut, int $guests): Booking
    {
        $checkIn = Carbon::parse($checkIn);
        $checkOut = Carbon::parse($checkOut);
        $nights = max(1, $checkIn->diffInDays($checkOut));

        $pricePerNight = (float) $property->price_per_night;
        $subtotal = round($nights * $pricePerNight, 2);
        $commissionRate = (float) ($property->vendor?->commission_rate ?? 15.00);
        $commissionAmount = round($subtotal * $commissionRate / 100, 2);
        $vendorPayout = round($subtotal - $commissionAmount, 2);

        $booking = Booking::create([
            'user_id' => $user->id,
            'property_id' => $property->id,
            'vendor_id' => $property->user_id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $guests,
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'subtotal' => $subtotal,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'vendor_payout_amount' => $vendorPayout,
            'total_price' => $subtotal,
            'currency' => $property->currency,
            'status' => 'pending',
        ]);

        $user->notify(new BookingReceivedNotification($booking));

        if ($property->vendor) {
            $property->vendor->notify(new NewBookingNotification($booking));
        }

        return $booking;
    }
}
