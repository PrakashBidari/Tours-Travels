<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\HandlesAdminInput;
use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TourPackageController extends Controller
{
    use HandlesAdminInput;

    public function index(Request $request): View
    {
        return view('backend.tour-packages.index', [
            'tours' => TourPackage::withCount('bookings')
                ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
                ->ordered()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('backend.tour-packages.form', ['tour' => new TourPackage(['category' => request('category', 'nepal'), 'is_active' => true, 'season' => 'all', 'rating' => 4.8, 'group_size' => '2 – 16'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug(TourPackage::class, $data['title']);

        TourPackage::create($data);

        return redirect()->route('dashboard.tour-packages.index')->with('status', "\"{$data['title']}\" created.");
    }

    public function edit(TourPackage $tourPackage): View
    {
        return view('backend.tour-packages.form', ['tour' => $tourPackage]);
    }

    public function update(Request $request, TourPackage $tourPackage): RedirectResponse
    {
        $data = $this->validated($request, $tourPackage);

        if ($request->boolean('regenerate_slug')) {
            $data['slug'] = $this->uniqueSlug(TourPackage::class, $data['title'], $tourPackage->id);
        }

        $tourPackage->update($data);

        return redirect()->route('dashboard.tour-packages.index')->with('status', "\"{$tourPackage->title}\" updated.");
    }

    public function destroy(TourPackage $tourPackage): RedirectResponse
    {
        $tourPackage->delete();

        return back()->with('status', "\"{$tourPackage->title}\" deleted.");
    }

    protected function validated(Request $request, ?TourPackage $tour = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', Rule::in(array_keys(config('travel.tour_categories')))],
            'destination' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:120'],
            'duration_nights' => ['required', 'integer', 'min:0', 'max:120'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'trip_style' => ['nullable', Rule::in(array_keys(config('travel.trip_styles')))],
            'season' => ['required', Rule::in(array_keys(config('travel.seasons')))],
            'difficulty' => ['nullable', 'string', 'max:60'],
            'max_altitude' => ['nullable', 'string', 'max:30'],
            'group_size' => ['nullable', 'string', 'max:30'],
            'hotel' => ['nullable', 'string', 'max:190'],
            'meals' => ['nullable', 'string', 'max:190'],
            'transport' => ['nullable', 'string', 'max:190'],
            'summary' => ['nullable', 'string', 'max:500'],
            'overview' => ['nullable', 'string'],
            'highlights' => ['nullable', 'string'],
            'itinerary' => ['nullable', 'string'],
            'includes' => ['nullable', 'string'],
            'excludes' => ['nullable', 'string'],
            'visa_info' => ['nullable', 'string', 'max:3000'],
            'map_embed_url' => ['nullable', 'url', 'max:2000'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
            'review_count' => ['nullable', 'integer', 'min:0'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:190'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            ...static::imageRules('images', true),
        ]);

        $data['overview'] = filled($data['overview'] ?? null) ? clean($data['overview']) : null;
        $data['highlights'] = $this->lines($data['highlights'] ?? null);
        $data['itinerary'] = $this->itinerary($data['itinerary'] ?? null);
        $data['includes'] = $this->lines($data['includes'] ?? null);
        $data['excludes'] = $this->lines($data['excludes'] ?? null);
        $data['images'] = $this->gallery($request, 'images', 'tours');
        $data['rating'] ??= $tour?->rating ?? 4.8;
        $data['review_count'] ??= 0;
        unset($data['images_list']);

        return $data;
    }
}
