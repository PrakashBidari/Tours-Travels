<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SMS notifications via Sparrow SMS (Nepal). Without SPARROW_SMS_TOKEN configured the
 * message is only written to the log, so bookings work the same in every environment.
 */
class SmsGateway
{
    public function send(?string $phone, string $message): void
    {
        $number = preg_replace('/\D/', '', (string) $phone);

        if (strlen($number) < 10) {
            return;
        }

        $token = config('services.sparrow_sms.token');

        if (! $token) {
            Log::info("[SMS placeholder] to {$number}: {$message}");

            return;
        }

        try {
            Http::asForm()->timeout(10)->post('https://api.sparrowsms.com/v2/sms/', [
                'token' => $token,
                'from' => config('services.sparrow_sms.from', 'RamTours'),
                'to' => substr($number, -10),
                'text' => $message,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
