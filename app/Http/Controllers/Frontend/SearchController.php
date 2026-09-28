<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    protected array $amenityPool = [
        'Free WiFi', 'Free parking', 'Swimming pool', 'Air conditioning', 'Breakfast included',
        'Pet friendly', 'Fitness center', 'Spa', 'Bar', 'Restaurant', 'Non-smoking rooms',
        'Airport shuttle', '24-hour front desk', 'Room service', 'Family rooms', 'Terrace',
    ];

    protected array $propertyTypes = [
        'hotel' => 'Hotels',
        'apartment' => 'Apartments',
        'resort' => 'Resorts',
        'villa' => 'Villas',
        'guest_house' => 'Guest houses',
    ];

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $query = Property::approved();

        if ($destinationId = $request->integer('destination_id')) {
            $query->where('destination_id', $destinationId);
        }

        if ($destination = $request->string('destination')->trim()->value()) {
            $query->where(function ($q) use ($destination) {
                $q->where('city', 'like', "%{$destination}%")
                    ->orWhere('country', 'like', "%{$destination}%")
                    ->orWhere('name', 'like', "%{$destination}%");
            });
        }

        if ($guests = $request->integer('guests')) {
            $query->where('max_guests', '>=', $guests);
        }

        if ($minPrice = $request->integer('min_price')) {
            $query->where('price_per_night', '>=', $minPrice);
        }

        if ($maxPrice = $request->integer('max_price')) {
            $query->where('price_per_night', '<=', $maxPrice);
        }

        if ($types = $request->input('type', [])) {
            $query->whereIn('property_type', $types);
        }

        if ($stars = $request->input('stars', [])) {
            $query->whereIn('stars', $stars);
        }

        if ($ratingMin = $request->input('rating_min')) {
            $query->where('rating', '>=', (float) $ratingMin);
        }

        if ($amenities = $request->input('amenities', [])) {
            foreach ($amenities as $amenity) {
                $query->whereJsonContains('amenities', $amenity);
            }
        }

        match ($request->input('sort', 'recommended')) {
            'price_low' => $query->orderBy('price_per_night'),
            'price_high' => $query->orderByDesc('price_per_night'),
            'rating' => $query->orderByDesc('rating'),
            'reviews' => $query->orderByDesc('review_count'),
            default => $query->orderByDesc('is_featured')->orderByDesc('rating'),
        };

        $properties = $query->paginate(10)->withQueryString();
        $view = $request->input('view') === 'list' ? 'list' : 'grid';

        if ($request->ajax()) {
            return view('frontend.partials.search-results', [
                'properties' => $properties,
                'view' => $view,
            ]);
        }

        return view('frontend.search', [
            'properties' => $properties,
            'amenityPool' => $this->amenityPool,
            'propertyTypes' => $this->propertyTypes,
            'view' => $view,
        ]);
    }
}
