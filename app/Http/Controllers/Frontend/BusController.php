<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use App\Models\BusSeatReservation;
use App\Models\ServiceBooking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BusController extends Controller
{
    public function index(): View
    {
        $popular = BusRoute::active()
            ->selectRaw('from_city, to_city, MIN(price) as min_price, COUNT(*) as buses')
            ->groupBy('from_city', 'to_city')
            ->orderByDesc('buses')
            ->take(9)
            ->get();

        return view('frontend.bus.index', ['popular' => $popular]);
    }

    public function search(Request $request): View
    {
        $data = $request->validate([
            'from' => ['required', 'string', 'max:60'],
            'to' => ['required', 'string', 'max:60'],
            'date' => ['nullable', 'date', 'after_or_equal:today'],
            'passengers' => ['nullable', 'integer', 'min:1', 'max:10'],
            'type' => ['nullable', 'string'],
        ]);

        $date = $data['date'] ?? now()->addDay()->toDateString();

        $routes = BusRoute::active()
            ->where('from_city', $data['from'])
            ->where('to_city', $data['to'])
            ->when($data['type'] ?? null, fn ($q, $type) => $q->where('bus_type', $type))
            ->orderBy('departure_time')
            ->get()
            ->each(fn (BusRoute $route) => $route->setAttribute('seats_left', $route->availableSeatCount($date)));

        return view('frontend.bus.search', [
            'routes' => $routes,
            'from' => $data['from'],
            'to' => $data['to'],
            'date' => $date,
            'passengers' => (int) ($data['passengers'] ?? 1),
        ]);
    }

    public function seats(Request $request, BusRoute $busRoute): View
    {
        abort_unless($busRoute->is_active, 404);

        $date = $request->date('date')?->isBefore(today()) === false
            ? $request->date('date')->toDateString()
            : now()->addDay()->toDateString();

        return view('frontend.bus.seats', [
            'route' => $busRoute,
            'date' => $date,
            'booked' => $busRoute->bookedSeats($date),
            'passengers' => max(1, min(10, $request->integer('passengers', 1))),
        ]);
    }

    public function book(Request $request, BusRoute $busRoute, BookingService $bookings): RedirectResponse
    {
        abort_unless($busRoute->is_active, 404);

        $data = $request->validate(array_merge(BookingService::travelerRules(), [
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'seats' => ['required', 'array', 'min:1', 'max:10'],
            'seats.*' => ['required', 'string', 'distinct', Rule::in($busRoute->allSeatLabels())],
        ]));

        $date = Carbon::parse($data['travel_date'])->toDateString();
        $seats = array_values($data['seats']);

        $taken = array_intersect($seats, $busRoute->bookedSeats($date));
        if ($taken) {
            throw ValidationException::withMessages(['seats' => __('Seat(s) :seats were just booked by someone else. Please choose different seats.', ['seats' => implode(', ', $taken)])]);
        }

        try {
            $booking = $bookings->create('bus', $busRoute, [
                'title' => "{$busRoute->from_city} → {$busRoute->to_city} · {$busRoute->bus_name}",
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'nationality' => $data['nationality'] ?? null,
                'travel_date' => $date,
                'adults' => count($seats),
                'special_request' => $data['special_request'] ?? null,
                'unit_price' => $busRoute->price,
                'quantity' => count($seats),
                'subtotal' => $busRoute->price * count($seats),
                'coupon_code' => $data['coupon_code'] ?? null,
                'details' => [
                    'operator' => $busRoute->operator,
                    'bus' => "{$busRoute->bus_name} ({$busRoute->type_label})",
                    'seats' => $seats,
                    'departure' => $busRoute->departure_label,
                    'arrival' => $busRoute->arrival_label,
                    'boarding_point' => $busRoute->boarding_point,
                    'dropping_point' => $busRoute->dropping_point,
                ],
            ], function (ServiceBooking $booking) use ($busRoute, $seats, $date) {
                foreach ($seats as $seat) {
                    BusSeatReservation::create([
                        'bus_route_id' => $busRoute->id,
                        'service_booking_id' => $booking->id,
                        'travel_date' => $date,
                        'seat_number' => $seat,
                    ]);
                }
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['seats' => __('One of your seats was just booked by someone else. Please choose again.')]);
        }

        return redirect(BookingService::confirmationUrl($booking))
            ->with('status', __('Seats reserved! Your booking ID is :ref.', ['ref' => $booking->reference]));
    }
}
