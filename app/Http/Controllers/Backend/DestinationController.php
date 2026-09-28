<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Notifications\DestinationStatusChangedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(Request $request): View
    {
        $destinations = Destination::with('vendor')
            ->withCount('properties')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->get();

        return view('backend.destinations.index', ['destinations' => $destinations]);
    }

    public function create(): View
    {
        return view('backend.destinations.form', ['destination' => new Destination]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = 'approved';

        Destination::create($data);

        return redirect()->route('dashboard.destinations.index')->with('status', 'Destination created.');
    }

    public function edit(Destination $destination): View
    {
        return view('backend.destinations.form', ['destination' => $destination]);
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $data = $this->validated($request);

        $destination->update($data);

        return redirect()->route('dashboard.destinations.index')->with('status', 'Destination updated.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $packageCount = $destination->properties()->count();
        $destination->properties()->delete();
        $destination->delete();

        $message = $packageCount > 0
            ? "\"{$destination->name}\" and its {$packageCount} package(s) have all been removed."
            : "\"{$destination->name}\" has been removed.";

        return back()->with('status', $message);
    }

    public function approve(Destination $destination): RedirectResponse
    {
        $destination->update(['status' => 'approved']);
        $destination->vendor?->notify(new DestinationStatusChangedNotification($destination));

        return back()->with('status', "\"{$destination->name}\" has been approved and is now live.");
    }

    public function reject(Destination $destination): RedirectResponse
    {
        $destination->update(['status' => 'rejected']);
        $destination->vendor?->notify(new DestinationStatusChangedNotification($destination));

        return back()->with('status', "\"{$destination->name}\" has been rejected.");
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url'],
            'is_featured' => ['sometimes', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('destinations', 'public');
        } elseif (! empty($data['image_url'])) {
            $data['image'] = $data['image_url'];
        } else {
            unset($data['image']);
        }

        unset($data['image_url']);
        $data['is_featured'] = $request->boolean('is_featured');

        if (! empty($data['description'])) {
            $data['description'] = clean($data['description']);
        }

        return $data;
    }
}
