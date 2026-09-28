<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Notifications\ServiceBookingUpdated;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Online payments. eSewa (ePay v2) and Khalti (KPG-2) are fully wired; the remaining
 * methods record the traveler's choice so staff can send a QR / payment link manually
 * until those merchant accounts are configured.
 */
class PaymentController extends Controller
{
    public function show(ServiceBooking $booking): View|RedirectResponse
    {
        if (! $booking->isPayable()) {
            return redirect(BookingService::confirmationUrl($booking))->with('status', __('This booking has no payment due.'));
        }

        return view('frontend.booking.pay', [
            'booking' => $booking,
            'methods' => config('travel.payment_methods'),
            'khaltiEnabled' => filled(config('travel.khalti.secret_key')),
        ]);
    }

    public function start(Request $request, ServiceBooking $booking): View|RedirectResponse
    {
        abort_unless($booking->isPayable(), 403);

        $method = $request->validate([
            'method' => ['required', Rule::in(array_keys(config('travel.payment_methods')))],
        ])['method'];

        return match ($method) {
            'esewa' => $this->esewaForm($booking),
            'khalti' => filled(config('travel.khalti.secret_key')) ? $this->khaltiInitiate($booking) : $this->manual($booking, $method),
            default => $this->manual($booking, $method),
        };
    }

    /** Methods without a live integration: record the choice and let staff follow up. */
    protected function manual(ServiceBooking $booking, string $method): RedirectResponse
    {
        $booking->update(['payment_method' => $method, 'payment_status' => 'pending']);

        $label = config("travel.payment_methods.{$method}.label");
        $message = $method === 'office'
            ? __('Please pay at our office in Gangabu or by bank transfer quoting booking ID :ref.', ['ref' => $booking->reference])
            : __('Thank you! Our team will send you a :method payment QR / link for booking :ref shortly.', ['method' => $label, 'ref' => $booking->reference]);

        return redirect(BookingService::confirmationUrl($booking))->with('status', $message);
    }

    // ---------------------------------------------------------------- eSewa

    protected function esewaForm(ServiceBooking $booking): View
    {
        $config = config('travel.esewa');
        $total = number_format((float) $booking->total, 2, '.', '');
        $uuid = $booking->reference.'-'.now()->format('His');

        $booking->update(['payment_method' => 'esewa', 'payment_status' => 'pending']);

        return view('frontend.booking.esewa-redirect', [
            'action' => $config['form_url'],
            'fields' => [
                'amount' => $total,
                'tax_amount' => '0',
                'total_amount' => $total,
                'transaction_uuid' => $uuid,
                'product_code' => $config['product_code'],
                'product_service_charge' => '0',
                'product_delivery_charge' => '0',
                'success_url' => route('payment.esewa.success'),
                'failure_url' => route('payment.esewa.failure', ['ref' => $booking->reference]),
                'signed_field_names' => 'total_amount,transaction_uuid,product_code',
                'signature' => $this->esewaSignature("total_amount={$total},transaction_uuid={$uuid},product_code={$config['product_code']}"),
            ],
        ]);
    }

    protected function esewaSignature(string $message): string
    {
        return base64_encode(hash_hmac('sha256', $message, config('travel.esewa.secret_key'), true));
    }

    public function esewaSuccess(Request $request): RedirectResponse
    {
        $payload = json_decode(base64_decode((string) $request->query('data')), true) ?: [];
        $booking = ServiceBooking::where('reference', explode('-', $payload['transaction_uuid'] ?? '')[0])->first();

        abort_unless($booking, 404);

        // 1) The response must be signed with our secret key…
        $fields = explode(',', $payload['signed_field_names'] ?? '');
        $message = collect($fields)->map(fn ($field) => $field.'='.($payload[$field] ?? ''))->implode(',');
        $validSignature = hash_equals($this->esewaSignature($message), (string) ($payload['signature'] ?? ''));

        // 2) …and eSewa's status API must confirm the transaction for the full amount.
        $status = rescue(fn () => Http::timeout(15)->get(config('travel.esewa.status_url'), [
            'product_code' => config('travel.esewa.product_code'),
            'total_amount' => $payload['total_amount'] ?? null,
            'transaction_uuid' => $payload['transaction_uuid'] ?? null,
        ])->json('status'), null, report: true);

        $amountMatches = abs((float) str_replace(',', '', (string) ($payload['total_amount'] ?? 0)) - (float) $booking->total) < 0.01;

        if ($validSignature && $amountMatches && ($payload['status'] ?? null) === 'COMPLETE' && $status === 'COMPLETE') {
            $this->paid($booking, 'esewa', $payload['transaction_code'] ?? $payload['transaction_uuid']);

            return redirect(BookingService::confirmationUrl($booking))->with('status', __('Payment successful via eSewa. Thank you!'));
        }

        $booking->update(['payment_status' => 'failed']);

        return redirect(URL::signedRoute('booking.pay', $booking))->with('error', __('We could not verify your eSewa payment. Please try again or contact us.'));
    }

    public function esewaFailure(Request $request): RedirectResponse
    {
        $booking = ServiceBooking::where('reference', $request->query('ref'))->firstOrFail();

        if (! $booking->isPaid()) {
            $booking->update(['payment_status' => 'unpaid']);
        }

        return redirect(URL::signedRoute('booking.pay', $booking))->with('error', __('eSewa payment was cancelled. You can try again or choose another method.'));
    }

    // ---------------------------------------------------------------- Khalti

    protected function khaltiInitiate(ServiceBooking $booking): RedirectResponse
    {
        $response = Http::withHeaders(['Authorization' => 'Key '.config('travel.khalti.secret_key')])
            ->timeout(15)
            ->post(config('travel.khalti.base_url').'epayment/initiate/', [
                'return_url' => route('payment.khalti.callback'),
                'website_url' => url('/'),
                'amount' => (int) round((float) $booking->total * 100),
                'purchase_order_id' => $booking->reference,
                'purchase_order_name' => str($booking->title)->limit(90)->value(),
                'customer_info' => ['name' => $booking->full_name, 'email' => $booking->email, 'phone' => $booking->phone],
            ]);

        if (! $response->successful() || ! $response->json('payment_url')) {
            report(new \RuntimeException('Khalti initiate failed: '.$response->body()));

            return back()->with('error', __('Khalti is unavailable right now. Please try another payment method.'));
        }

        $booking->update(['payment_method' => 'khalti', 'payment_status' => 'pending', 'transaction_id' => $response->json('pidx')]);

        return redirect()->away($response->json('payment_url'));
    }

    public function khaltiCallback(Request $request): RedirectResponse
    {
        $booking = ServiceBooking::where('reference', $request->query('purchase_order_id'))->firstOrFail();

        $lookup = rescue(fn () => Http::withHeaders(['Authorization' => 'Key '.config('travel.khalti.secret_key')])
            ->timeout(15)
            ->post(config('travel.khalti.base_url').'epayment/lookup/', ['pidx' => $request->query('pidx')])
            ->json(), [], report: true);

        $amountMatches = (int) ($lookup['total_amount'] ?? 0) === (int) round((float) $booking->total * 100);

        if (($lookup['status'] ?? null) === 'Completed' && $amountMatches && $request->query('pidx') === $booking->transaction_id) {
            $this->paid($booking, 'khalti', $lookup['transaction_id'] ?? $request->query('pidx'));

            return redirect(BookingService::confirmationUrl($booking))->with('status', __('Payment successful via Khalti. Thank you!'));
        }

        $booking->update(['payment_status' => 'failed']);

        return redirect(URL::signedRoute('booking.pay', $booking))->with('error', __('Khalti payment was not completed.'));
    }

    protected function paid(ServiceBooking $booking, string $method, ?string $transactionId): void
    {
        if ($booking->isPaid()) {
            return;
        }

        $booking->markPaid($method, $transactionId);

        rescue(fn () => Notification::route('mail', $booking->email)->notify(new ServiceBookingUpdated($booking)));
    }
}
