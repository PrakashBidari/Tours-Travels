<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        $upcoming = $user->bookings()->with('property')
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '>=', now())
            ->orderBy('check_in')
            ->take(5)
            ->get();

        $stats = [
            'upcoming_trips' => $user->bookings()->whereIn('status', ['pending', 'confirmed'])->where('check_in', '>=', now())->count(),
            'completed_trips' => $user->bookings()->where('status', 'completed')->count(),
            'wishlist_count' => $user->wishlists()->count(),
            'cart_count' => $user->cartItems()->count(),
        ];

        return view('account.dashboard', ['stats' => $stats, 'upcoming' => $upcoming]);
    }
}
