<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Services\BookingService;
use App\Support\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public booking pages. Travelers can book without an account, so the confirmation,
 * invoice and payment pages are protected by signed URLs instead of login.
 */
class BookingController extends Controller
{
    public function show(ServiceBooking $booking): View
    {
        $url = BookingService::confirmationUrl($booking);

        return view('frontend.booking.show', [
            'booking' => $booking->load('bookable'),
            'qr' => QrCode::svg($url, 150),
            'invoiceUrl' => URL::signedRoute('booking.invoice', $booking),
            'payUrl' => URL::signedRoute('booking.pay', $booking),
            'shareUrl' => $url,
        ]);
    }

    public function invoice(ServiceBooking $booking): Response
    {
        $pdf = Pdf::loadView('frontend.booking.invoice', [
            'booking' => $booking,
            'qr' => QrCode::dataUri(BookingService::confirmationUrl($booking), 110),
        ])->setPaper('a4');

        return $pdf->download("RamTours-{$booking->reference}.pdf");
    }

    public function trackForm(): View
    {
        return view('frontend.booking.track');
    }

    public function track(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:30'],
            'contact' => ['required', 'string', 'max:150'],
        ]);

        $contact = trim($data['contact']);
        $digits = preg_replace('/\D/', '', $contact);

        $booking = ServiceBooking::where('reference', strtoupper(trim($data['reference'])))->first();

        $matches = $booking && (
            strcasecmp($booking->email, $contact) === 0
            || (strlen($digits) >= 7 && str_ends_with(preg_replace('/\D/', '', $booking->phone), substr($digits, -9)))
        );

        if (! $matches) {
            return back()->withInput()->withErrors(['reference' => __('We could not find a booking with those details. Please check your booking ID and email / phone.')]);
        }

        return redirect(BookingService::confirmationUrl($booking));
    }
}
