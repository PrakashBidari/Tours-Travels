<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeOrderController extends Controller
{
    /**
     * Super-admin-only screen to control the display order of destinations (home page
     * "Popular destinations" + the public destinations listing) and featured packages
     * (home page "Featured stays").
     */
    public function index(): View
    {
        $destinations = Destination::approved()->ordered()->get();
        $packages = Property::approved()->where('is_featured', true)->ordered()->get();

        return view('backend.home-order.index', [
            'destinations' => $destinations,
            'packages' => $packages,
        ]);
    }

    public function updateDestinations(Request $request): JsonResponse
    {
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:destinations,id'],
        ])['order'];

        foreach ($order as $index => $id) {
            Destination::whereKey($id)->update(['position' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function updatePackages(Request $request): JsonResponse
    {
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:properties,id'],
        ])['order'];

        foreach ($order as $index => $id) {
            Property::whereKey($id)->update(['position' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }
}
