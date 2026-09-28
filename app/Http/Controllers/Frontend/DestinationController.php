<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Destination::approved()
            ->with('vendor')
            ->withCount(['properties' => fn ($q) => $q->approved()]);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($vendorId = $request->integer('vendor_id')) {
            $query->where('user_id', $vendorId);
        }

        $destinations = $query->ordered()->paginate(12)->withQueryString();

        $vendors = User::where('role', 'vendor')
            ->whereHas('destinations', fn ($q) => $q->approved())
            ->orderBy('company_name')
            ->orderBy('name')
            ->get(['id', 'name', 'company_name']);

        return view('frontend.destinations.index', [
            'destinations' => $destinations,
            'vendors' => $vendors,
        ]);
    }
}
