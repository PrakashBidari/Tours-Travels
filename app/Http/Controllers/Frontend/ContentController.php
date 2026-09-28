<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocale;
use App\Models\BlogPost;
use App\Models\BusRoute;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\Offer;
use App\Models\Subscriber;
use App\Models\Testimonial;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VisaService;
use App\Notifications\NewInquiryNotification;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Offers, blog, gallery, reviews, FAQ, policies and small site utilities. */
class ContentController extends Controller
{
    public function offers(): View
    {
        return view('frontend.offers', [
            'offers' => Offer::live()->orderBy('expires_at')->get(),
            'deals' => TourPackage::active()->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->ordered()->take(8)->get(),
        ]);
    }

    public function blog(Request $request): View
    {
        return view('frontend.blog.index', [
            'posts' => BlogPost::published()
                ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
                ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString(),
            'categories' => BlogPost::published()->select('category')->distinct()->pluck('category'),
        ]);
    }

    public function post(BlogPost $post): View
    {
        abort_unless($post->published_at && ! $post->published_at->isFuture(), 404);

        $post->increment('views');

        return view('frontend.blog.show', [
            'post' => $post,
            'recent' => BlogPost::published()->whereKeyNot($post->id)->latest('published_at')->take(4)->get(),
        ]);
    }

    public function gallery(Request $request): View
    {
        return view('frontend.gallery', [
            'items' => GalleryItem::ordered()->when($request->filled('album'), fn ($q) => $q->where('album', $request->string('album')))->get(),
            'albums' => GalleryItem::select('album')->distinct()->orderBy('album')->pluck('album'),
        ]);
    }

    public function testimonials(): View
    {
        $reviews = Testimonial::approved()->ordered()->get();

        return view('frontend.testimonials', [
            'reviews' => $reviews,
            'average' => round((float) $reviews->avg('rating'), 1),
        ]);
    }

    public function storeTestimonial(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'min:20', 'max:1500'],
        ]);

        Testimonial::create($data + ['source' => 'website', 'is_approved' => false]);

        return back()->with('status', __('Thank you for your review! It will appear once approved by our team.'));
    }

    public function faq(): View
    {
        return view('frontend.faq', [
            'faqs' => Faq::active()->ordered()->get()->groupBy('category'),
        ]);
    }

    public function refundPolicy(): View
    {
        return view('frontend.pages.refund');
    }

    public function cookiePolicy(): View
    {
        return view('frontend.pages.cookies');
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:150']]);

        Subscriber::firstOrCreate(['email' => strtolower($data['email'])]);

        return back()->with('status', __('You are subscribed! Watch your inbox for travel deals.'));
    }

    /** Quote requests / general inquiries from the contact page, tour pages and CTA forms. */
    public function inquiry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(config('travel.inquiry_types')))],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:25'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $inquiry = Inquiry::create($data);

        rescue(fn () => Notification::send(User::adminsFor('inquiries'), new NewInquiryNotification($inquiry)));

        return back()->with('status', __('Thank you! A travel consultant will get back to you within a few hours.'));
    }

    public function locale(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, SetLocale::LOCALES, true), 404);

        $request->session()->put('locale', $locale);

        return back();
    }

    public function currency(Request $request): RedirectResponse
    {
        $data = $request->validate(['currency' => ['required', Rule::in(array_keys(config('travel.currencies')))]]);

        $request->session()->put('currency', $data['currency']);

        return back();
    }

    /** AJAX coupon preview used by the booking forms. */
    public function checkCoupon(Request $request, BookingService $bookings): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:40'],
            'amount' => ['required', 'numeric', 'min:0'],
            'service' => ['required', Rule::in(array_keys(config('travel.service_types')))],
        ]);

        try {
            [$discount] = $bookings->applyCoupon($data['code'], (float) $data['amount'], $data['service']);
        } catch (ValidationException $e) {
            return response()->json(['valid' => false, 'message' => $e->validator->errors()->first()], 422);
        }

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'message' => __('Coupon applied! You save :amount.', ['amount' => npr($discount)]),
        ]);
    }

    public function sitemap(): Response
    {
        $urls = collect([
            [route('home'), '1.0'], [route('about'), '0.6'], [route('contact'), '0.6'],
            [route('tours.index'), '0.9'], [route('flights.index'), '0.9'], [route('bus.index'), '0.9'],
            [route('cars.index'), '0.9'], [route('visa.index'), '0.9'], [route('search'), '0.8'],
            [route('offers.index'), '0.8'], [route('blog.index'), '0.7'], [route('gallery.index'), '0.5'],
            [route('testimonials.index'), '0.5'], [route('faq'), '0.5'],
        ])->map(fn ($row) => ['loc' => $row[0], 'priority' => $row[1], 'lastmod' => null]);

        foreach (['tours.show' => TourPackage::active()->get(['slug', 'updated_at']), 'cars.show' => Vehicle::active()->get(['slug', 'updated_at']), 'visa.show' => VisaService::active()->get(['slug', 'updated_at']), 'blog.show' => BlogPost::published()->get(['slug', 'updated_at'])] as $route => $models) {
            foreach ($models as $model) {
                $urls->push(['loc' => route($route, $model->slug), 'priority' => '0.7', 'lastmod' => $model->updated_at?->toAtomString()]);
            }
        }

        foreach (BusRoute::active()->select('from_city', 'to_city')->distinct()->get() as $route) {
            $urls->push(['loc' => route('bus.search', ['from' => $route->from_city, 'to' => $route->to_city]), 'priority' => '0.6', 'lastmod' => null]);
        }

        return response()->view('frontend.sitemap', ['urls' => $urls], 200)->header('Content-Type', 'application/xml');
    }
}
