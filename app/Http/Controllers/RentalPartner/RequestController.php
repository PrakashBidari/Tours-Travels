<?php

namespace App\Http\Controllers\RentalPartner;

use App\Http\Controllers\Controller;
use App\Models\VehicleRentalRequest;
use App\Notifications\VehicleRentalRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RequestController extends Controller
{
    public function index(): View
    {
        $requests = VehicleRentalRequest::where('status', 'pending')->with('traveller')->latest()->get();

        return view('rental-partner.requests.index', compact('requests'));
    }

    public function applied(): View
    {
        $requests = VehicleRentalRequest::where('rental_partner_id', Auth::id())
            ->whereIn('status', ['claimed', 'accepted', 'completed'])
            ->with('traveller')
            ->latest()
            ->get();

        return view('rental-partner.requests.applied', compact('requests'));
    }

    public function claim(VehicleRentalRequest $vehicleRentalRequest): RedirectResponse
    {
        abort_unless($vehicleRentalRequest->status === 'pending' && $vehicleRentalRequest->rental_partner_id === null, 409, 'This request has already been claimed.');

        $vehicleRentalRequest->update([
            'status' => 'claimed',
            'rental_partner_id' => Auth::id(),
        ]);

        $partner = Auth::user();

        $vehicleRentalRequest->traveller->notify(new VehicleRentalRequestNotification(
            $vehicleRentalRequest,
            "{$partner->company_name} wants to pick you up for your {$vehicleRentalRequest->pickup_location} → {$vehicleRentalRequest->dropoff_location} ride. Please accept or reject.",
            route('account.rental-requests.index')
        ));

        return back()->with('status', 'Request claimed — the traveller has been notified.');
    }

    public function unclaim(Request $request, VehicleRentalRequest $vehicleRentalRequest): RedirectResponse
    {
        abort_unless($vehicleRentalRequest->rental_partner_id === Auth::id(), 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $vehicleRentalRequest->update([
            'status' => 'pending',
            'rental_partner_id' => null,
        ]);

        $partnerName = Auth::user()->company_name;

        $vehicleRentalRequest->traveller->notify(new VehicleRentalRequestNotification(
            $vehicleRentalRequest,
            "{$partnerName} cancelled the pickup: {$validated['reason']}. Your request is available to other partners again.",
            route('account.rental-requests.index')
        ));

        return back()->with('status', 'Claim cancelled.');
    }

    public function complete(VehicleRentalRequest $vehicleRentalRequest): RedirectResponse
    {
        abort_unless($vehicleRentalRequest->rental_partner_id === Auth::id() && $vehicleRentalRequest->status === 'accepted', 403);

        $vehicleRentalRequest->update(['status' => 'completed']);

        return back()->with('status', 'Marked as completed.');
    }
}
