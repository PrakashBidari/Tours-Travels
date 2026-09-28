<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\HandlesAdminInput;
use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Offers & deals; an offer with a code is also a checkout coupon. */
class OfferController extends Controller
{
    use HandlesAdminInput;

    public function index(): View
    {
        return view('backend.offers.index', ['offers' => Offer::latest()->get()]);
    }

    public function create(): View
    {
        return view('backend.offers.form', ['offer' => new Offer(['is_active' => true, 'discount_type' => 'percent', 'applies_to' => 'all', 'starts_at' => now(), 'expires_at' => now()->addMonth()])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Offer::create($this->validated($request));

        return redirect()->route('dashboard.offers.index')->with('status', 'Offer created.');
    }

    public function edit(Offer $offer): View
    {
        return view('backend.offers.form', ['offer' => $offer]);
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $offer->update($this->validated($request, $offer));

        return redirect()->route('dashboard.offers.index')->with('status', 'Offer updated.');
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $offer->delete();

        return back()->with('status', 'Offer deleted.');
    }

    protected function validated(Request $request, ?Offer $offer = null): array
    {
        $request->merge(['code' => $request->filled('code') ? strtoupper(trim($request->input('code'))) : null]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'code' => ['nullable', 'alpha_dash', 'max:40', Rule::unique('offers', 'code')->ignore($offer?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0', $request->input('discount_type') === 'percent' ? 'max:100' : 'max:10000000'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'applies_to' => ['required', Rule::in(array_merge(['all'], array_keys(config('travel.service_types'))))],
            'badge' => ['nullable', 'string', 'max:40'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
            ...static::imageRules('image'),
        ]);

        $data['min_amount'] ??= 0;
        $data['image'] = $this->singleImage($request, 'image', 'offers', $offer?->image);
        unset($data['image_url']);

        return $data;
    }
}
