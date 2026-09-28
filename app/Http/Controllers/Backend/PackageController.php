<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Property;
use App\Models\User;
use App\Notifications\PackageStatusChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $packages = Property::with('vendor')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->get();

        return view('backend.packages.index', ['packages' => $packages]);
    }

    public function create(): View
    {
        return view('backend.packages.form', [
            'package' => new Property,
            'destinations' => Destination::orderBy('name')->get(),
            'vendors' => User::where('role', 'vendor')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['rating'] = 0;
        $data['review_count'] = 0;
        $data['images'] = $this->resolveImages($request);

        Property::create($data);

        return redirect()->route('dashboard.packages.index')->with('status', 'Package created.');
    }

    public function edit(Property $package): View
    {
        return view('backend.packages.form', [
            'package' => $package,
            'destinations' => Destination::orderBy('name')->get(),
            'vendors' => User::where('role', 'vendor')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Property $package): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = $data['user_id'] ?? $package->user_id;

        $destination = $data['destination_id']
            ? Destination::find($data['destination_id'])
            : $package->destination;

        if ($data['status'] === 'approved' && $destination && $destination->status !== 'approved') {
            return back()->withInput()->with('error', "Please approve the destination \"{$destination->name}\" first before approving this package.");
        }

        $newImages = $this->resolveImages($request);
        if (! empty($newImages)) {
            $data['images'] = $newImages;
        }

        $package->update($data);

        return redirect()->route('dashboard.packages.index')->with('status', 'Package updated.');
    }

    public function updateCategory(Request $request, Property $package): RedirectResponse
    {
        $data = $request->validate([
            'home_section' => ['required', 'in:default,popular,trending'],
        ]);

        $package->update($data);

        return back()->with('status', "\"{$package->name}\" category updated.");
    }

    public function destroy(Property $package): RedirectResponse
    {
        $package->delete();

        return back()->with('status', 'Package removed.');
    }

    public function approve(Property $package): RedirectResponse
    {
        if ($package->destination && $package->destination->status !== 'approved') {
            return back()->with('error', "Please approve the destination \"{$package->destination->name}\" first before approving \"{$package->name}\".");
        }

        $package->update(['status' => 'approved']);
        $package->vendor?->notify(new PackageStatusChangedNotification($package));

        return back()->with('status', "\"{$package->name}\" has been approved and is now live.");
    }

    public function reject(Property $package): RedirectResponse
    {
        $package->update(['status' => 'rejected']);
        $package->vendor?->notify(new PackageStatusChangedNotification($package));

        return back()->with('status', "\"{$package->name}\" has been rejected.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'destination_id' => ['nullable', 'exists:destinations,id'],
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
            'home_section' => ['required', 'in:default,popular,trending'],
            'status' => ['required', 'in:pending,approved,rejected'],
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
