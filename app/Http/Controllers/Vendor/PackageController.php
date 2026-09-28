<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(): View
    {
        $packages = Auth::user()->properties()->with('destination')->latest()->get();

        return view('vendor.packages.index', ['packages' => $packages]);
    }

    public function create(): View
    {
        $destinations = Auth::user()->destinations()->orderBy('name')->get();

        return view('vendor.packages.form', ['package' => new Property, 'destinations' => $destinations]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = 'pending';
        $data['rating'] = 0;
        $data['review_count'] = 0;
        $data['images'] = $this->resolveImages($request);

        Property::create($data);

        return redirect()->route('vendor.packages.index')->with('status', 'Package submitted for admin approval.');
    }

    public function edit(Property $package): View
    {
        $this->authorizeOwner($package);

        $destinations = Auth::user()->destinations()->orderBy('name')->get();

        return view('vendor.packages.form', ['package' => $package, 'destinations' => $destinations]);
    }

    public function update(Request $request, Property $package): RedirectResponse
    {
        $this->authorizeOwner($package);

        $data = $this->validated($request);
        $data['status'] = 'pending';

        $newImages = $this->resolveImages($request);
        if (! empty($newImages)) {
            $data['images'] = $newImages;
        }

        $package->update($data);

        return redirect()->route('vendor.packages.index')->with('status', 'Package updated and resubmitted for approval.');
    }

    public function destroy(Property $package): RedirectResponse
    {
        $this->authorizeOwner($package);
        $package->delete();

        return back()->with('status', 'Package removed.');
    }

    protected function authorizeOwner(Property $package): void
    {
        abort_unless($package->user_id === Auth::id(), 403);
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'destination_id' => ['nullable', Rule::exists('destinations', 'id')->where('user_id', Auth::id())],
            'type' => ['required', 'in:hotel,tour,destination'],
            'property_type' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'stars' => ['nullable', 'integer', 'min:1', 'max:5'],
            'max_guests' => ['required', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
            'images' => ['nullable', 'string'],
            'image_files' => ['nullable', 'array'],
            'image_files.*' => ['image', 'max:4096'],
            'amenities' => ['nullable', 'string'],
        ]);

        unset($data['images'], $data['image_files']);

        $data['amenities'] = collect(explode(',', $request->input('amenities', '')))->map(fn ($a) => trim($a))->filter()->values()->all();
        $data['is_featured'] = $request->boolean('is_featured');
        $data['description'] = clean($data['description']);

        return $data;
    }

    protected function resolveImages(Request $request): array
    {
        $uploaded = collect($request->file('image_files', []))->map(fn ($file) => $file->store('packages', 'public'));
        $urls = collect(explode(',', $request->input('images', '')))->map(fn ($url) => trim($url))->filter();

        return $uploaded->merge($urls)->values()->all();
    }
}
