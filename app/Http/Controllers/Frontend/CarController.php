<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CarController extends Controller
{
    public const SERVICES = ['Airport Pickup & Drop', 'Tour & Sightseeing', 'Wedding Car', 'Corporate Rental', 'Self Drive'];

    public function index(Request $request): View
    {
        $vehicles = Vehicle::active()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($request->input('mode') === 'self', fn ($q) => $q->where('self_drive', true))
            ->when($request->integer('seats'), fn ($q, $seats) => $q->where('seats', '>=', $seats))
            ->when($request->filled('service'), fn ($q) => $q->whereJsonContains('services', $request->string('service')->value()))
            ->orderByDesc('is_featured')
            ->orderBy('price_per_day')
            ->get();

        return view('frontend.cars.index', [
            'vehicles' => $vehicles,
            'search' => $request->only(['pickup', 'from', 'to', 'category', 'mode', 'seats', 'service']),
        ]);
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        abort_unless($vehicle->is_active, 404);

        return view('frontend.cars.show', [
            'vehicle' => $vehicle,
            'search' => $request->only(['pickup', 'from', 'to']),
            'related' => Vehicle::active()->whereKeyNot($vehicle->id)->where('category', $vehicle->category)->take(3)->get(),
        ]);
    }

    public function book(Request $request, Vehicle $vehicle, BookingService $bookings): RedirectResponse
    {
        abort_unless($vehicle->is_active, 404);

        $data = $request->validate(array_merge(BookingService::travelerRules(), [
            'pickup_location' => ['required', 'string', 'max:200'],
            'dropoff_location' => ['nullable', 'string', 'max:200'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after_or_equal:pickup_date'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'drive_mode' => ['required', Rule::in($vehicle->self_drive ? ['driver', 'self'] : ['driver'])],
            'service' => ['nullable', Rule::in(self::SERVICES)],
            'passengers' => ['required', 'integer', 'min:1', 'max:'.$vehicle->seats],
            'license_number' => ['nullable', 'required_if:drive_mode,self', 'string', 'max:40'],
        ]));

        if ($vehicle->isBookedBetween($data['pickup_date'], $data['return_date'])) {
            throw ValidationException::withMessages(['pickup_date' => __('This vehicle is already booked for some of those dates. Please pick other dates or another vehicle.')]);
        }

        $days = Carbon::parse($data['pickup_date'])->diffInDays(Carbon::parse($data['return_date'])) + 1;
        $dailyRate = (float) $vehicle->price_per_day + ($data['drive_mode'] === 'driver' ? (float) $vehicle->driver_charge_per_day : 0);

        $booking = $bookings->create('car', $vehicle, [
            'title' => "{$vehicle->name} · {$days} ".str('day')->plural($days),
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'nationality' => $data['nationality'] ?? null,
            'travel_date' => $data['pickup_date'],
            'return_date' => $data['return_date'],
            'adults' => $data['passengers'],
            'special_request' => $data['special_request'] ?? null,
            'unit_price' => $dailyRate,
            'quantity' => $days,
            'subtotal' => $dailyRate * $days,
            'coupon_code' => $data['coupon_code'] ?? null,
            'details' => [
                'vehicle' => $vehicle->name.' ('.$vehicle->category_label.')',
                'pickup_location' => $data['pickup_location'],
                'dropoff_location' => $data['dropoff_location'] ?? $data['pickup_location'],
                'pickup_time' => $data['pickup_time'],
                'drive_mode' => $data['drive_mode'] === 'self' ? 'Self Drive' : 'With Driver',
                'service' => $data['service'] ?? null,
                'days' => $days,
                'license_number' => $data['license_number'] ?? null,
            ],
        ]);

        return redirect(BookingService::confirmationUrl($booking))
            ->with('status', __('Vehicle booked! Your booking ID is :ref.', ['ref' => $booking->reference]));
    }
}
