<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** The traveler's tour, flight, bus, car and visa bookings (made signed in, or as a guest with the same email). */
class TravelBookingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('account.travel-bookings.index', [
            'bookings' => ServiceBooking::where(fn ($q) => $q->where('user_id', $user->id)->orWhere('email', $user->email))
                ->latest()
                ->get(),
        ]);
    }
}
