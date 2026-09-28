<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $properties = Auth::user()->wishlists()->with('property')->latest()->get()->pluck('property');

        return view('account.wishlist.index', ['properties' => $properties]);
    }

    public function toggle(Request $request, Property $property): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $wishlist = Auth::user()->wishlists()->where('property_id', $property->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $wishlisted = false;
        } else {
            Auth::user()->wishlists()->create(['property_id' => $property->id]);
            $wishlisted = true;
        }

        if ($request->wantsJson()) {
            return response()->json(['wishlisted' => $wishlisted]);
        }

        return back();
    }
}
