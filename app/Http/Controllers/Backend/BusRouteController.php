<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\HandlesAdminInput;
use App\Http\Controllers\Controller;
use App\Models\BusRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BusRouteController extends Controller
{
    use HandlesAdminInput;

    public function index(Request $request): View
    {
        $date = $request->date('date')?->toDateString() ?? today()->toDateString();

        $routes = BusRoute::orderBy('from_city')->orderBy('to_city')->orderBy('departure_time')->get()
            ->each(fn (BusRoute $route) => $route->setAttribute('booked_count', count($route->bookedSeats($date))));

        return view('backend.bus-routes.index', ['routes' => $routes, 'date' => $date]);
    }

    public function create(): View
    {
        return view('backend.bus-routes.form', ['route' => new BusRoute(['is_active' => true, 'total_seats' => 35, 'bus_type' => 'tourist'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $route = BusRoute::create($this->validated($request));

        return redirect()->route('dashboard.bus-routes.index')->with('status', "Route {$route->from_city} → {$route->to_city} ({$route->bus_name}) added.");
    }

    public function edit(BusRoute $busRoute): View
    {
        return view('backend.bus-routes.form', ['route' => $busRoute]);
    }

    public function update(Request $request, BusRoute $busRoute): RedirectResponse
    {
        $busRoute->update($this->validated($request, $busRoute));

        return redirect()->route('dashboard.bus-routes.index')->with('status', 'Route updated.');
    }

    public function destroy(BusRoute $busRoute): RedirectResponse
    {
        $busRoute->delete();

        return back()->with('status', 'Route deleted.');
    }

    /** Passenger manifest / seat map for one departure date. */
    public function seats(Request $request, BusRoute $busRoute): View
    {
        $date = $request->date('date')?->toDateString() ?? today()->toDateString();

        $reservations = $busRoute->seatReservations()
            ->with('booking')
            ->whereDate('travel_date', $date)
            ->get()
            ->keyBy('seat_number');

        return view('backend.bus-routes.seats', ['route' => $busRoute, 'date' => $date, 'reservations' => $reservations]);
    }

    protected function validated(Request $request, ?BusRoute $route = null): array
    {
        $data = $request->validate([
            'operator' => ['required', 'string', 'max:120'],
            'bus_name' => ['required', 'string', 'max:120'],
            'bus_type' => ['required', Rule::in(array_keys(BusRoute::TYPES))],
            'from_city' => ['required', 'string', 'max:60'],
            'to_city' => ['required', 'string', 'max:60', 'different:from_city'],
            'departure_time' => ['required', 'date_format:H:i'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'boarding_point' => ['nullable', 'string', 'max:190'],
            'dropping_point' => ['nullable', 'string', 'max:190'],
            'price' => ['required', 'numeric', 'min:0'],
            'total_seats' => ['required', 'integer', 'min:5', 'max:60'],
            'amenities' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            ...static::imageRules('image'),
        ]);

        $data['amenities'] = $this->lines($data['amenities'] ?? null);
        $data['image'] = $this->singleImage($request, 'image', 'buses', $route?->image);
        unset($data['image_url']);

        return $data;
    }
}
