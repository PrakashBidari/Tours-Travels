<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $vendor = Auth::user();

        if (! $vendor->isApprovedVendor()) {
            return view('vendor.dashboard', ['stats' => null, 'recentBookings' => collect()]);
        }

        $bookings = Booking::where('vendor_id', $vendor->id);

        $stats = [
            'total_listings' => $vendor->properties()->count(),
            'pending_listings' => $vendor->properties()->where('status', 'pending')->count(),
            'total_bookings' => (clone $bookings)->count(),
            'upcoming_checkins' => (clone $bookings)->whereIn('status', ['pending', 'confirmed'])->where('check_in', '>=', now())->count(),
            'total_payout' => (clone $bookings)->whereIn('status', ['confirmed', 'completed'])->sum('vendor_payout_amount'),
        ];

        $recentBookings = (clone $bookings)->with(['user', 'property'])->latest()->take(8)->get();

        return view('vendor.dashboard', ['stats' => $stats, 'recentBookings' => $recentBookings]);
    }
}
