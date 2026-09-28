<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TripController extends Controller
{
    public function index(): View
    {
        $bookings = Auth::user()->bookings()->with(['property', 'vendor'])->latest()->get();

        return view('account.trips.index', ['bookings' => $bookings]);
    }

    /**
     * Let a traveler remove an upcoming trip they booked by mistake.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::id(), 403);

        $isUpcoming = in_array($booking->status, ['pending', 'confirmed'], true)
            && $booking->check_in->greaterThanOrEqualTo(now()->startOfDay());

        abort_unless($isUpcoming, 422, 'Only upcoming trips can be deleted.');

        $booking->delete();

        return back()->with('status', 'Trip deleted.');
    }
}
