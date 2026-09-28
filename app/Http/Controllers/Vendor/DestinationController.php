<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DestinationController extends Controller
{
    public function index(): View
    {
        $destinations = Auth::user()->destinations()->withCount('properties')->latest()->get();

        return view('vendor.destinations.index', ['destinations' => $destinations]);
    }

    public function create(): View
    {
        return view('vendor.destinations.form', ['destination' => new Destination]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = 'pending';

        Destination::create($data);

        return redirect()->route('vendor.destinations.index')->with('status', 'Destination submitted for admin approval.');
    }

    public function edit(Destination $destination): View
    {
        $this->authorizeOwner($destination);

        return view('vendor.destinations.form', ['destination' => $destination]);
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $this->authorizeOwner($destination);

        $data = $this->validated($request);
        $data['status'] = 'pending';

        $destination->update($data);

        return redirect()->route('vendor.destinations.index')->with('status', 'Destination updated and resubmitted for approval.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        $this->authorizeOwner($destination);

        $packageCount = $destination->properties()->count();
        $destination->properties()->delete();
        $destination->delete();

        $message = $packageCount > 0
            ? "\"{$destination->name}\" and its {$packageCount} package(s) have all been removed."
            : "\"{$destination->name}\" has been removed.";

        return back()->with('status', $message);
    }

    protected function authorizeOwner(Destination $destination): void
    {
        abort_unless($destination->user_id === Auth::id(), 403);
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
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('destinations', 'public');
        } elseif (! empty($data['image_url'])) {
            $data['image'] = $data['image_url'];
        } else {
            unset($data['image']);
        }

        unset($data['image_url']);

        if (! empty($data['description'])) {
            $data['description'] = clean($data['description']);
        }

        return $data;
    }
}
