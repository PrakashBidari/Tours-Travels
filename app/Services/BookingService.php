<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Notifications\NewServiceBookingNotification;
use App\Notifications\ServiceBookingConfirmation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

/** Creates bookings for every service and handles coupons + notifications in one place. */
class BookingService
{
    public function __construct(protected SmsGateway $sms) {}

    /** Validation rules for the traveler details every booking form collects. */
    public static function travelerRules(bool $passportRequired = false): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'min:7', 'max:25'],
            'nationality' => ['nullable', 'string', 'max:80'],
            'passport_number' => [$passportRequired ? 'required' : 'nullable', 'string', 'max:30'],
            'special_request' => ['nullable', 'string', 'max:2000'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes  ServiceBooking columns (title, dates, pricing, details…).
     * @param  callable|null  $afterCreate  Runs inside the transaction (e.g. to reserve bus seats).
     */
    public function create(string $serviceType, ?Model $bookable, array $attributes, ?callable $afterCreate = null): ServiceBooking
    {
        $subtotal = round((float) ($attributes['subtotal'] ?? 0), 2);
        [$discount, $offer] = $this->applyCoupon($attributes['coupon_code'] ?? null, $subtotal, $serviceType);

        $booking = DB::transaction(function () use ($serviceType, $bookable, $attributes, $subtotal, $discount, $offer, $afterCreate) {
            $booking = ServiceBooking::create(array_merge($attributes, [
                'user_id' => Auth::id(),
                'service_type' => $serviceType,
                'bookable_type' => $bookable?->getMorphClass(),
                'bookable_id' => $bookable?->getKey(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => max(0, $subtotal - $discount),
                'coupon_code' => $offer?->code,
                'currency' => 'NPR',
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]));

            $offer?->increment('used_count');

            if ($afterCreate) {
                $afterCreate($booking);
            }

            return $booking;
        });

        $this->notify($booking);

        return $booking;
    }

    /** @return array{0: float, 1: Offer|null} */
    public function applyCoupon(?string $code, float $amount, string $serviceType): array
    {
        if (! filled($code)) {
            return [0.0, null];
        }

        $offer = Offer::where('code', strtoupper(trim($code)))->first();

        if (! $offer) {
            throw ValidationException::withMessages(['coupon_code' => __('Invalid coupon code.')]);
        }

        $result = $offer->discountFor($amount, $serviceType);

        if (is_string($result)) {
            throw ValidationException::withMessages(['coupon_code' => $result]);
        }

        return [$result, $offer];
    }

    /** Signed link to the booking confirmation page (bookings may be made without an account). */
    public static function confirmationUrl(ServiceBooking $booking): string
    {
        return URL::signedRoute('booking.show', $booking);
    }

    /**
     * Customer email + SMS and admin alerts. Failures are reported but never undo the
     * booking — it is already saved and visible in the dashboard.
     */
    protected function notify(ServiceBooking $booking): void
    {
        try {
            Notification::route('mail', $booking->email)->notify(new ServiceBookingConfirmation($booking));
        } catch (\Throwable $e) {
            report($e);
        }

        try {
            Notification::send(User::adminsFor('bookings'), new NewServiceBookingNotification($booking));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->sms->send($booking->phone, "Ram Tours: Your {$booking->service_label} booking {$booking->reference} is received. We will confirm shortly. Call ".site('phone'));
    }
}
