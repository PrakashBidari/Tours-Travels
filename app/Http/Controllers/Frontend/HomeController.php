<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\GalleryItem;
use App\Models\Offer;
use App\Models\Testimonial;
use App\Models\TourPackage;
use App\Models\Vehicle;
use App\Models\VisaService;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** The six "Explore Nepal's Beauty" cards: destination => [tagline, image key]. */
    protected const NEPAL_DESTINATIONS = [
        'Kathmandu' => ['Culture & Heritage', 'kathmandu'],
        'Pokhara' => ['Lakes & Mountains', 'pokhara'],
        'Chitwan' => ['Wildlife & Jungle Safari', 'chitwan'],
        'Lumbini' => ['Birthplace of Buddha', 'lumbini'],
        'Mustang' => ['Adventure & Culture', 'mustang'],
        'Everest Region' => ['Trekking & Mountaineering', 'ama_dablam'],
    ];

    protected const INTERNATIONAL = ['Dubai', 'Thailand', 'Bali', 'Singapore', 'Maldives', 'Europe'];

    public function __invoke(): View
    {
        $packageCounts = TourPackage::active()
            ->selectRaw('destination, count(*) as total')
            ->groupBy('destination')
            ->pluck('total', 'destination');

        $nepalDestinations = collect(self::NEPAL_DESTINATIONS)->map(fn ($meta, $name) => [
            'name' => $name,
            'tagline' => $meta[0],
            'image' => config('travel.images.'.$meta[1]),
            'count' => $packageCounts[$name] ?? 0,
            'url' => route('tours.index', ['destination' => $name]),
        ])->values();

        return view('frontend.home', [
            'nepalDestinations' => $nepalDestinations,
            'internationalDestinations' => self::INTERNATIONAL,
            'hotDeals' => TourPackage::active()->where('is_featured', true)->whereIn('category', ['nepal', 'international'])->ordered()->take(8)->get(),
            'adventures' => TourPackage::active()->whereIn('category', ['trekking', 'adventure'])->ordered()->take(4)->get(),
            'testimonials' => Testimonial::approved()->ordered()->take(8)->get(),
            'posts' => BlogPost::published()->latest('published_at')->take(3)->get(),
            'gallery' => GalleryItem::where('type', 'image')->ordered()->take(8)->get(),
            'offers' => Offer::live()->latest()->take(3)->get(),
            'featuredVehicles' => Vehicle::active()->where('is_featured', true)->take(4)->get(),
            'tourDestinations' => TourPackage::active()->orderBy('destination')->distinct()->pluck('destination'),
            'visaCountries' => VisaService::active()->orderBy('country')->distinct()->pluck('country'),
        ]);
    }
}
