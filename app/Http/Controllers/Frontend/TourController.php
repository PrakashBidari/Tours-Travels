<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Services\BookingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/** Nepal, international, trekking and adventure packages. */
class TourController extends Controller
{
    public const DURATIONS = ['1-3' => '1 – 3 days', '4-7' => '4 – 7 days', '8-14' => '8 – 14 days', '15-99' => '15+ days'];

    public function index(Request $request): View
    {
        $category = $request->string('category')->value();

        $tours = TourPackage::active()
            // ?saved=slug-a,slug-b lists the visitor's browser wishlist.
            ->when($request->filled('saved'), fn ($q) => $q->whereIn('slug', array_slice(explode(',', (string) $request->query('saved')), 0, 50)))
            ->when(array_key_exists($category, config('travel.tour_categories')), fn ($q) => $q->where('category', $category))
            ->when($request->filled('destination'), fn ($q) => $q->where(fn ($q) => $q
                ->where('destination', $request->string('destination'))
                ->orWhere('country', $request->string('destination'))))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($q) => $q
                ->where('title', 'like', '%'.$request->string('q').'%')
                ->orWhere('destination', 'like', '%'.$request->string('q').'%')))
            ->when($request->filled('duration') && array_key_exists($request->input('duration'), self::DURATIONS), function ($q) use ($request) {
                [$min, $max] = explode('-', $request->input('duration'));
                $q->whereBetween('duration_days', [(int) $min, (int) $max]);
            })
            ->when($request->filled('season'), fn ($q) => $q->whereIn('season', [$request->string('season'), 'all']))
            ->when($request->filled('style'), fn ($q) => $q->where('trip_style', $request->string('style')))
            ->when($request->integer('min_price'), fn ($q, $min) => $q->whereRaw('COALESCE(sale_price, price) >= ?', [$min]))
            ->when($request->integer('max_price'), fn ($q, $max) => $q->whereRaw('COALESCE(sale_price, price) <= ?', [$max]));

        match ($request->input('sort')) {
            'price_asc' => $tours->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $tours->orderByRaw('COALESCE(sale_price, price) desc'),
            'duration' => $tours->orderBy('duration_days'),
            'rating' => $tours->orderByDesc('rating'),
            default => $tours->ordered(),
        };

        return view('frontend.tours.index', [
            'tours' => $tours->paginate(12)->withQueryString(),
            'category' => $category,
            'destinations' => TourPackage::active()
                ->when($category, fn ($q) => $q->where('category', $category))
                ->orderBy('destination')->distinct()->pluck('destination'),
            'durations' => self::DURATIONS,
        ]);
    }

    public function show(TourPackage $tour): View
    {
        abort_unless($tour->is_active, 404);

        return view('frontend.tours.show', [
            'tour' => $tour,
            'related' => TourPackage::active()
                ->whereKeyNot($tour->id)
                ->where(fn ($q) => $q->where('destination', $tour->destination)->orWhere('category', $tour->category))
                ->ordered()->take(4)->get(),
        ]);
    }

    public function itinerary(TourPackage $tour): Response
    {
        abort_unless($tour->is_active, 404);

        return Pdf::loadView('frontend.tours.itinerary-pdf', ['tour' => $tour])
            ->setPaper('a4')
            ->download($tour->slug.'-itinerary.pdf');
    }

    /** Side-by-side comparison of up to 3 packages (?tours=slug-a,slug-b). */
    public function compare(Request $request): View
    {
        $slugs = array_slice(array_filter(explode(',', (string) $request->query('tours'))), 0, 3);

        $tours = TourPackage::active()->whereIn('slug', $slugs)->get()
            ->sortBy(fn ($tour) => array_search($tour->slug, $slugs))->values();

        return view('frontend.tours.compare', ['tours' => $tours]);
    }

    public function book(Request $request, TourPackage $tour, BookingService $bookings): RedirectResponse
    {
        abort_unless($tour->is_active, 404);

        $data = $request->validate(array_merge(BookingService::travelerRules($tour->category === 'international'), [
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'adults' => ['required', 'integer', 'min:1', 'max:50'],
            'children' => ['nullable', 'integer', 'min:0', 'max:50'],
        ]));

        $children = (int) ($data['children'] ?? 0);
        // Children (under 12) are charged half the adult price.
        $subtotal = $tour->final_price * $data['adults'] + $tour->final_price * 0.5 * $children;

        $booking = $bookings->create('tour', $tour, [
            'title' => $tour->title,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'nationality' => $data['nationality'] ?? null,
            'passport_number' => $data['passport_number'] ?? null,
            'travel_date' => $data['travel_date'],
            'return_date' => now()->parse($data['travel_date'])->addDays(max(0, $tour->duration_days - 1))->toDateString(),
            'adults' => $data['adults'],
            'children' => $children,
            'special_request' => $data['special_request'] ?? null,
            'unit_price' => $tour->final_price,
            'quantity' => $data['adults'] + $children,
            'subtotal' => $subtotal,
            'coupon_code' => $data['coupon_code'] ?? null,
            'details' => [
                'category' => $tour->category_label,
                'destination' => $tour->destination,
                'duration' => $tour->duration_label,
                'hotel' => $tour->hotel,
                'meals' => $tour->meals,
                'transport' => $tour->transport,
            ],
        ]);

        return redirect(BookingService::confirmationUrl($booking))->with('status', __('Booking received! Your booking ID is :ref.', ['ref' => $booking->reference]));
    }
}
