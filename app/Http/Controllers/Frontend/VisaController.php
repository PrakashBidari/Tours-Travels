<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\VisaService;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisaController extends Controller
{
    public function index(Request $request): View
    {
        $visas = VisaService::active()
            ->when($request->filled('country'), fn ($q) => $q->where('country', $request->string('country')))
            ->when($request->filled('type'), fn ($q) => $q->where('visa_type', $request->string('type')))
            ->orderByDesc('is_featured')
            ->orderBy('country')
            ->get();

        return view('frontend.visa.index', [
            'visas' => $visas,
            'countries' => VisaService::active()->orderBy('country')->distinct()->pluck('country'),
        ]);
    }

    public function show(VisaService $visa): View
    {
        abort_unless($visa->is_active, 404);

        return view('frontend.visa.show', [
            'visa' => $visa,
            'others' => VisaService::active()->whereKeyNot($visa->id)->where(fn ($q) => $q->where('country', $visa->country)->orWhere('visa_type', $visa->visa_type))->take(4)->get(),
        ]);
    }

    public function apply(Request $request, VisaService $visa, BookingService $bookings): RedirectResponse
    {
        abort_unless($visa->is_active, 404);

        $data = $request->validate(array_merge(BookingService::travelerRules(true), [
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'applicants' => ['required', 'integer', 'min:1', 'max:20'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]));

        // Applicant documents contain passport scans, so they go to the private disk
        // and are only downloadable by staff from the dashboard.
        $documents = collect($request->file('documents', []))->map(fn ($file) => [
            'name' => $file->getClientOriginalName(),
            'path' => $file->store('visa-documents', 'local'),
        ])->values()->all();

        $booking = $bookings->create('visa', $visa, [
            'title' => $visa->title,
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'nationality' => $data['nationality'] ?? 'Nepali',
            'passport_number' => $data['passport_number'],
            'travel_date' => $data['travel_date'] ?? null,
            'adults' => $data['applicants'],
            'special_request' => $data['special_request'] ?? null,
            'unit_price' => $visa->total_fee,
            'quantity' => $data['applicants'],
            'subtotal' => $visa->total_fee * $data['applicants'],
            'coupon_code' => $data['coupon_code'] ?? null,
            'details' => [
                'country' => $visa->country,
                'visa_type' => $visa->type_label,
                'processing_time' => $visa->processing_time,
                'documents' => $documents,
            ],
        ]);

        return redirect(BookingService::confirmationUrl($booking))
            ->with('status', __('Visa application received! Our visa desk will review your documents within one working day.'));
    }
}
