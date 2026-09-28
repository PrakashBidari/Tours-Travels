<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Notifications\BookingStatusChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'property'])
            ->where('vendor_id', Auth::id())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->get();

        return view('vendor.bookings.index', ['bookings' => $bookings]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->vendor_id === Auth::id(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:confirmed,completed,cancelled'],
            'cancelled_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $booking->update($data);
        $booking->user->notify(new BookingStatusChangedNotification($booking));

        return back()->with('status', "Booking {$booking->reference} marked as {$data['status']}.");
    }
}
