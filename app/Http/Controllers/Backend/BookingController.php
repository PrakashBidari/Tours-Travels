<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::with(['user', 'property', 'vendor'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('vendor_id'), fn ($q) => $q->where('vendor_id', $request->integer('vendor_id')))
            ->latest()
            ->get();

        $vendors = User::where('role', 'vendor')->where('vendor_status', 'approved')->orderBy('name')->get();

        return view('backend.bookings.index', ['bookings' => $bookings, 'vendors' => $vendors]);
    }
}
