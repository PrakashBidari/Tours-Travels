<?php

namespace App\Http\Controllers\RentalPartner;

use App\Http\Controllers\Controller;
use App\Models\VehicleRentalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $partner = Auth::user();

        if (! $partner->isApprovedRentalPartner()) {
            return view('rental-partner.dashboard', ['stats' => null, 'recentRequests' => collect()]);
        }

        $stats = [
            'available_count' => VehicleRentalRequest::where('status', 'pending')->count(),
            'applied_count' => VehicleRentalRequest::where('rental_partner_id', $partner->id)->whereIn('status', ['claimed', 'accepted'])->count(),
            'completed_count' => VehicleRentalRequest::where('rental_partner_id', $partner->id)->where('status', 'completed')->count(),
        ];

        $recentRequests = VehicleRentalRequest::where('status', 'pending')->with('traveller')->latest()->take(8)->get();

        return view('rental-partner.dashboard', compact('stats', 'recentRequests'));
    }
}
