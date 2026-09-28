<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Concerns\CreatesBookings;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Book Now / cart checkout no longer create a Booking directly — they land here
 * first so the traveler confirms (and, first time round, fills in) their contact
 * details before anything is persisted.
 */
class BookingDetailsController extends Controller
{
    use CreatesBookings;

    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
        ]);

        session(['pending_booking' => array_merge(['mode' => 'single'], $data)]);

        return redirect()->route('account.booking.details');
    }

    public function startCheckout(): RedirectResponse
    {
        abort_if(Auth::user()->cartItems()->doesntExist(), 422, 'Your cart is empty.');

        session(['pending_booking' => ['mode' => 'cart']]);

        return redirect()->route('account.booking.details');
    }

    public function edit(): View|RedirectResponse
    {
        $pending = session('pending_booking');

        if (! $pending) {
            return redirect()->route('search')->with('error', 'Please choose a stay to book first.');
        }

        $items = $this->resolveItems($pending);

        if ($items->isEmpty()) {
            session()->forget('pending_booking');

            return redirect()->route('account.cart.index')->with('error', 'That booking is no longer available.');
        }

        return view('account.booking.details', [
            'user' => Auth::user(),
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(session()->has('pending_booking'), 422, 'Your booking session has expired. Please start again.');

        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        Auth::user()->update($data);

        return redirect()->route('account.booking.confirm');
    }

    public function confirm(): View|RedirectResponse
    {
        $pending = session('pending_booking');

        if (! $pending) {
            return redirect()->route('search')->with('error', 'Please choose a stay to book first.');
        }

        $user = Auth::user();

        if (blank($user->phone) || blank($user->address)) {
            return redirect()->route('account.booking.details');
        }

        $items = $this->resolveItems($pending);

        if ($items->isEmpty()) {
            session()->forget('pending_booking');

            return redirect()->route('account.cart.index')->with('error', 'That booking is no longer available.');
        }

        return view('account.booking.confirm', [
            'user' => $user,
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ]);
    }

    public function store(): RedirectResponse
    {
        $pending = session('pending_booking');
        abort_unless($pending, 422, 'Your booking session has expired. Please start again.');

        $user = Auth::user();
        abort_if(blank($user->phone) || blank($user->address), 422, 'Please add your contact details first.');

        $items = $this->resolveItems($pending);
        abort_if($items->isEmpty(), 422, 'Nothing to book.');

        DB::transaction(function () use ($items, $user, $pending) {
            foreach ($items as $item) {
                $this->createBooking($user, $item['property'], $item['check_in'], $item['check_out'], $item['guests']);
            }

            if ($pending['mode'] === 'cart') {
                $user->cartItems()->delete();
            }
        });

        session()->forget('pending_booking');

        return redirect()->route('account.trips.index')->with('status', 'Booking confirmed! You can see it under My Trips.');
    }

    /**
     * @return Collection<int, array{property: Property, check_in: Carbon, check_out: Carbon, guests: int, nights: int, subtotal: float}>
     */
    protected function resolveItems(array $pending): Collection
    {
        if ($pending['mode'] === 'cart') {
            return Auth::user()->cartItems()->with('property')->get()->map(fn ($item) => [
                'property' => $item->property,
                'check_in' => $item->check_in,
                'check_out' => $item->check_out,
                'guests' => $item->guests,
                'nights' => $item->nights(),
                'subtotal' => $item->subtotal(),
            ]);
        }

        $property = Property::find($pending['property_id'] ?? null);

        if (! $property) {
            return collect();
        }

        $checkIn = Carbon::parse($pending['check_in']);
        $checkOut = Carbon::parse($pending['check_out']);
        $nights = max(1, $checkIn->diffInDays($checkOut));

        return collect([[
            'property' => $property,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $pending['guests'],
            'nights' => $nights,
            'subtotal' => round($nights * (float) $property->price_per_night, 2),
        ]]);
    }
}
