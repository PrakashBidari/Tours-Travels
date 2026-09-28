<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $items = Auth::user()->cartItems()->with('property')->latest()->get();

        return view('account.cart.index', ['items' => $items]);
    }

    public function store(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
        ]);

        Auth::user()->cartItems()->create($data);

        if ($request->wantsJson()) {
            $count = Auth::user()->cartItems()->count();

            return response()->json(['status' => 'Added to your cart.', 'count' => $count]);
        }

        return back()->with('status', 'Added to your cart.');
    }

    public function destroy(Request $request, int $cartItem): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        Auth::user()->cartItems()->where('id', $cartItem)->delete();

        if ($request->wantsJson()) {
            $items = Auth::user()->cartItems()->with('property')->latest()->get();

            return response()->json([
                'status' => 'Removed from your cart.',
                'count' => $items->count(),
                'total' => $items->sum(fn ($i) => $i->subtotal()),
                'currency' => optional($items->first())->property->currency ?? null,
            ]);
        }

        return back()->with('status', 'Removed from your cart.');
    }
}
