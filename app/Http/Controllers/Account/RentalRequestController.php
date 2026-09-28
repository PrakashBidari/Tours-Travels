<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\VehicleRentalRequest;
use App\Notifications\VehicleRentalRequestNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RentalRequestController extends Controller
{
    public function index(): View
    {
        $requests = Auth::user()->vehicleRentalRequests()->with('rentalPartner')->latest()->get();

        $statusStyles = [
            'pending' => 'bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-500/10 dark:text-gray-400',
            'claimed' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
            'accepted' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
            'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400',
        ];

        $statusLabels = [
            'pending' => 'Waiting for a partner',
            'claimed' => 'Awaiting your response',
            'accepted' => 'Tracking — in progress',
            'completed' => 'Completed',
        ];

        return view('account.rental-requests.index', compact('requests', 'statusStyles', 'statusLabels'));
    }

    public function accept(VehicleRentalRequest $vehicleRentalRequest): RedirectResponse
    {
        abort_unless($vehicleRentalRequest->user_id === Auth::id() && $vehicleRentalRequest->status === 'claimed', 403);

        $vehicleRentalRequest->update(['status' => 'accepted']);

        $vehicleRentalRequest->rentalPartner?->notify(new VehicleRentalRequestNotification(
            $vehicleRentalRequest,
            'Your pickup was accepted — trip is now in progress.',
            route('rental-partner.requests.applied')
        ));

        return back()->with('status', 'Accepted — enjoy your ride!');
    }

    public function reject(Request $request, VehicleRentalRequest $vehicleRentalRequest): RedirectResponse
    {
        abort_unless($vehicleRentalRequest->user_id === Auth::id() && $vehicleRentalRequest->status === 'claimed', 403);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $originalPartner = $vehicleRentalRequest->rentalPartner;

        $vehicleRentalRequest->update(['status' => 'pending', 'rental_partner_id' => null]);

        $originalPartner?->notify(new VehicleRentalRequestNotification(
            $vehicleRentalRequest,
            "The traveller declined your pickup offer: {$data['reason']}. The request is open to all partners again.",
            route('rental-partner.requests.index')
        ));

        return back()->with('status', 'Declined — the request is open to other partners again.');
    }

    public function destroy(Request $request, VehicleRentalRequest $vehicleRentalRequest): RedirectResponse
    {
        abort_unless($vehicleRentalRequest->user_id === Auth::id(), 403);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        if ($vehicleRentalRequest->rental_partner_id) {
            $partner = $vehicleRentalRequest->rentalPartner;

            $partner?->notify(new VehicleRentalRequestNotification(
                $vehicleRentalRequest,
                "The traveller cancelled their ride request. Reason: {$data['reason']}",
                route('rental-partner.requests.applied')
            ));
        }

        $vehicleRentalRequest->delete();

        return back()->with('status', 'Request deleted.');
    }
}
