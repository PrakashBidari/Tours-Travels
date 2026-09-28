<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VehicleRentalRequest;
use App\Notifications\VehicleRentalRequestNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VehicleRentalController extends Controller
{
    public function create(): View
    {
        return view('frontend.vehicle-rental.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'vehicle_type' => ['required', 'in:taxi,bike,other'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'dropoff_location' => ['required', 'string', 'max:255'],
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'pickup_time' => ['required', 'date_format:H:i'],
        ]);

        $data['user_id'] = Auth::id();

        $vehicleRentalRequest = VehicleRentalRequest::create($data);

        User::where('role', 'rental_partner')->where('vendor_status', 'approved')->get()
            ->each(fn (User $partner) => $partner->notify(new VehicleRentalRequestNotification(
                $vehicleRentalRequest,
                "New ride request: {$vehicleRentalRequest->pickup_location} \u{2192} {$vehicleRentalRequest->dropoff_location} on {$vehicleRentalRequest->pickup_date->format('M j, Y')} at ".Carbon::parse($vehicleRentalRequest->pickup_time)->format('g:i A'),
                route('rental-partner.requests.index')
            )));

        return redirect()->route('account.rental-requests.index')->with('status', 'Your ride request has been sent to nearby rental partners.');
    }
}
