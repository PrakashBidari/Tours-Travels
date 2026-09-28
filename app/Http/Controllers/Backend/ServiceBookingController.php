<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Notifications\ServiceBookingUpdated;
use App\Services\BookingService;
use App\Services\SmsGateway;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Tour, flight, bus, car and visa bookings (hotel bookings stay under Bookings). */
class ServiceBookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $this->filtered($request)->latest()->paginate(25)->withQueryString();

        $counts = ServiceBooking::selectRaw('service_type, COUNT(*) as total')->groupBy('service_type')->pluck('total', 'service_type');

        return view('backend.service-bookings.index', [
            'bookings' => $bookings,
            'counts' => $counts,
        ]);
    }

    public function show(ServiceBooking $serviceBooking): View
    {
        return view('backend.service-bookings.show', [
            'booking' => $serviceBooking->load(['bookable', 'user', 'seatReservations']),
            'confirmationUrl' => BookingService::confirmationUrl($serviceBooking),
        ]);
    }

    public function update(Request $request, ServiceBooking $serviceBooking, SmsGateway $sms): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(config('travel.booking_statuses'))],
            'payment_status' => ['required', Rule::in(config('travel.payment_statuses'))],
            'payment_method' => ['nullable', Rule::in(array_keys(config('travel.payment_methods')))],
            'transaction_id' => ['nullable', 'string', 'max:120'],
            'pnr' => ['nullable', 'string', 'max:30'],
            'ticket_number' => ['nullable', 'string', 'max:60'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'notify_customer' => ['sometimes', 'boolean'],
        ]);

        $subtotal = (float) ($data['subtotal'] ?? $serviceBooking->subtotal);
        $discount = min((float) ($data['discount'] ?? $serviceBooking->discount), $subtotal);

        $serviceBooking->fill([
            'status' => $data['status'],
            'payment_status' => $data['payment_status'],
            'payment_method' => $data['payment_method'] ?? $serviceBooking->payment_method,
            'transaction_id' => $data['transaction_id'] ?? $serviceBooking->transaction_id,
            'pnr' => $data['pnr'] ?? null,
            'ticket_number' => $data['ticket_number'] ?? null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $subtotal - $discount,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        if ($serviceBooking->isDirty('payment_status') && $serviceBooking->payment_status === 'paid') {
            $serviceBooking->paid_at ??= now();
        }

        $serviceBooking->save();

        // Cancelled / refunded bus bookings release their seats for resale.
        if (in_array($serviceBooking->status, ['cancelled', 'refunded'], true)) {
            $serviceBooking->seatReservations()->delete();
        }

        if ($request->boolean('notify_customer')) {
            rescue(fn () => Notification::route('mail', $serviceBooking->email)->notify(new ServiceBookingUpdated($serviceBooking)));
            $sms->send($serviceBooking->phone, "Ram Tours: Booking {$serviceBooking->reference} is now {$serviceBooking->status}. Payment: {$serviceBooking->payment_status}.");
        }

        return back()->with('status', "Booking {$serviceBooking->reference} updated.");
    }

    public function destroy(ServiceBooking $serviceBooking): RedirectResponse
    {
        foreach ($serviceBooking->details['documents'] ?? [] as $document) {
            Storage::disk('local')->delete($document['path']);
        }

        $serviceBooking->delete();

        return redirect()->route('dashboard.service-bookings.index')->with('status', "Booking {$serviceBooking->reference} deleted.");
    }

    /** Visa documents are on the private disk; only staff can download them. */
    public function document(ServiceBooking $serviceBooking, int $index): StreamedResponse
    {
        $document = $serviceBooking->details['documents'][$index] ?? abort(404);

        abort_unless(Storage::disk('local')->exists($document['path']), 404);

        return Storage::disk('local')->download($document['path'], $document['name']);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request)->latest();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Reference', 'Service', 'Title', 'Customer', 'Email', 'Phone', 'Travel date', 'Travelers', 'Total (NPR)', 'Status', 'Payment', 'Method', 'PNR', 'Ticket', 'Created']);

            $query->chunk(500, function ($bookings) use ($out) {
                foreach ($bookings as $b) {
                    fputcsv($out, [
                        $b->reference, $b->service_label, $b->title, $b->full_name, $b->email, $b->phone,
                        $b->travel_date?->toDateString(), $b->adults + $b->children, $b->total, $b->status,
                        $b->payment_status, $b->payment_method_label, $b->pnr, $b->ticket_number, $b->created_at->toDateTimeString(),
                    ]);
                }
            });

            fclose($out);
        }, 'ram-tours-bookings-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    protected function filtered(Request $request): Builder
    {
        return ServiceBooking::query()
            ->when($request->filled('service'), fn ($q) => $q->where('service_type', $request->string('service')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('payment'), fn ($q) => $q->where('payment_status', $request->string('payment')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $q->where(fn ($q) => $q->where('reference', 'like', $term)
                    ->orWhere('full_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('pnr', 'like', $term)
                    ->orWhere('title', 'like', $term));
            });
    }
}
