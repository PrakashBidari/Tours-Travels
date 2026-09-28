<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\User;
use App\Notifications\NewInquiryNotification;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Flight tickets are sold through our ticketing desk (no live GDS feed), so a search
 * becomes a booking request that staff price, issue and attach a PNR / e-ticket to.
 */
class FlightController extends Controller
{
    public function index(Request $request): View
    {
        $scope = $request->input('scope') === 'domestic' ? 'domestic' : ($request->input('scope') === 'international' ? 'international' : null);

        return view('frontend.flights.index', [
            'scope' => $scope,
            'search' => $request->only(['trip_type', 'from', 'to', 'depart', 'return', 'adults', 'cabin']),
            'airports' => config('travel.airports'),
        ]);
    }

    public function book(Request $request, BookingService $bookings): RedirectResponse
    {
        $airports = array_keys(config('travel.airports'));

        $data = $request->validate(array_merge(BookingService::travelerRules(), [
            'trip_type' => ['required', Rule::in(['oneway', 'round', 'multi'])],
            'from' => ['required', Rule::in($airports)],
            'to' => ['required', Rule::in($airports), 'different:from'],
            'depart' => ['required', 'date', 'after_or_equal:today'],
            'return' => ['nullable', 'required_if:trip_type,round', 'date', 'after_or_equal:depart'],
            'adults' => ['required', 'integer', 'min:1', 'max:9'],
            'children' => ['nullable', 'integer', 'min:0', 'max:9'],
            'infants' => ['nullable', 'integer', 'min:0', 'max:4'],
            'cabin' => ['required', Rule::in(['economy', 'premium', 'business', 'first'])],
            'airline' => ['nullable', 'string', 'max:80'],
            'multi_city_notes' => ['nullable', 'string', 'max:1000'],
        ]));

        $domestic = collect(config('travel.airports'))->keys()->take(12);
        $isDomestic = $domestic->contains($data['from']) && $domestic->contains($data['to']);
        $route = "{$data['from']} → {$data['to']}".($data['trip_type'] === 'round' ? " → {$data['from']}" : '');

        $booking = $bookings->create('flight', null, [
            'title' => ($isDomestic ? 'Domestic' : 'International')." Flight {$route}",
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'nationality' => $data['nationality'] ?? null,
            'passport_number' => $data['passport_number'] ?? null,
            'travel_date' => $data['depart'],
            'return_date' => $data['return'] ?? null,
            'adults' => $data['adults'],
            'children' => (int) ($data['children'] ?? 0),
            'special_request' => $data['special_request'] ?? null,
            'quantity' => $data['adults'] + (int) ($data['children'] ?? 0) + (int) ($data['infants'] ?? 0),
            // Fare is quoted by the ticketing desk after checking live availability.
            'subtotal' => 0,
            'details' => [
                'scope' => $isDomestic ? 'Domestic' : 'International',
                'trip_type' => ['oneway' => 'One Way', 'round' => 'Round Trip', 'multi' => 'Multi City'][$data['trip_type']],
                'from' => config('travel.airports.'.$data['from']),
                'to' => config('travel.airports.'.$data['to']),
                'cabin' => ucfirst($data['cabin']),
                'infants' => (int) ($data['infants'] ?? 0),
                'preferred_airline' => $data['airline'] ?? null,
                'multi_city' => $data['multi_city_notes'] ?? null,
                'promo_code' => $data['coupon_code'] ?? null,
            ],
        ]);

        return redirect(BookingService::confirmationUrl($booking))
            ->with('status', __('Flight request received! Our ticketing team will send you the best fare shortly.'));
    }

    /** PNR / fare / reissue / refund requests. */
    public function inquiry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['pnr', 'fare', 'reissue', 'refund'])],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:25'],
            'pnr' => ['nullable', 'required_if:type,pnr,reissue,refund', 'string', 'max:20'],
            'airline' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $inquiry = Inquiry::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => config('travel.inquiry_types.'.$data['type']).($data['pnr'] ?? null ? " — PNR {$data['pnr']}" : ''),
            'message' => $data['message'] ?? null,
            'data' => array_filter(['pnr' => $data['pnr'] ?? null, 'airline' => $data['airline'] ?? null]),
        ]);

        rescue(fn () => Notification::send(User::adminsFor('inquiries'), new NewInquiryNotification($inquiry)));

        return back()->with('status', __('Request received! Our ticketing team will contact you shortly.'));
    }
}
