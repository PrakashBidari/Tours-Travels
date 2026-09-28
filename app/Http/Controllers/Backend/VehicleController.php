<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\HandlesAdminInput;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleController extends Controller
{
    use HandlesAdminInput;

    public function index(): View
    {
        return view('backend.vehicles.index', [
            'vehicles' => Vehicle::withCount(['bookings' => fn ($q) => $q->whereNotIn('status', ['cancelled', 'refunded'])->where('return_date', '>=', today())])
                ->orderBy('category')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('backend.vehicles.form', ['vehicle' => new Vehicle(['is_active' => true, 'with_driver' => true, 'seats' => 4, 'luggage' => 2, 'transmission' => 'Manual', 'fuel' => 'Diesel'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(Vehicle::class, $data['name']);

        Vehicle::create($data);

        return redirect()->route('dashboard.vehicles.index')->with('status', "\"{$data['name']}\" added.");
    }

    /** Edit form plus the vehicle's upcoming bookings as an availability calendar. */
    public function edit(Vehicle $vehicle): View
    {
        return view('backend.vehicles.form', [
            'vehicle' => $vehicle,
            'upcoming' => $vehicle->bookings()
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->where('return_date', '>=', today())
                ->orderBy('travel_date')
                ->get(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($this->validated($request));

        return redirect()->route('dashboard.vehicles.index')->with('status', "\"{$vehicle->name}\" updated.");
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return back()->with('status', "\"{$vehicle->name}\" deleted.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', Rule::in(array_keys(config('travel.vehicle_categories')))],
            'seats' => ['required', 'integer', 'min:1', 'max:60'],
            'luggage' => ['required', 'integer', 'min:0', 'max:60'],
            'transmission' => ['required', 'string', 'max:30'],
            'fuel' => ['required', 'string', 'max:30'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'driver_charge_per_day' => ['required', 'numeric', 'min:0'],
            'self_drive' => ['boolean'],
            'with_driver' => ['boolean'],
            'services' => ['nullable', 'string'],
            'features' => ['nullable', 'string'],
            'description' => ['nullable', 'string', 'max:3000'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            ...static::imageRules('images', true),
        ]);

        $data['services'] = $this->lines($data['services'] ?? null);
        $data['features'] = $this->lines($data['features'] ?? null);
        $data['images'] = $this->gallery($request, 'images', 'vehicles');
        unset($data['images_list']);

        return $data;
    }
}
