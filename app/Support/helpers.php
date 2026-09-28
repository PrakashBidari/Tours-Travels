<?php

use App\Support\Site;
use Illuminate\Support\Facades\Storage;

if (! function_exists('media_url')) {
    /**
     * Resolve a stored image value to a URL. Values may be absolute URLs (seeded /
     * pasted by admins) or paths on the public disk (uploaded files).
     */
    function media_url(?string $path, ?string $fallback = null): string
    {
        if (! $path) {
            return $fallback ?? asset('images/placeholder.svg');
        }

        return str_starts_with($path, 'http') ? $path : Storage::disk('public')->url($path);
    }
}

if (! function_exists('npr')) {
    /** Format an amount stored in NPR, e.g. "Rs. 12,500". */
    function npr(float|int|string|null $amount, bool $decimals = false): string
    {
        return 'Rs. '.number_format((float) $amount, $decimals ? 2 : 0);
    }
}

if (! function_exists('money')) {
    /**
     * Display an NPR amount in the visitor's selected currency (header switcher).
     * Display only — bookings are always charged in NPR.
     */
    function money(float|int|string|null $amount): string
    {
        $code = session('currency', config('travel.currency'));
        $currency = config('travel.currencies.'.$code);

        if (! $currency || $code === 'NPR') {
            return npr($amount);
        }

        $converted = (float) $amount * $currency['rate'];

        return $currency['symbol'].' '.number_format($converted, $converted < 100 ? 2 : 0);
    }
}

if (! function_exists('site')) {
    /** Company/contact setting with admin overrides applied, e.g. site('phone'). */
    function site(string $key, mixed $default = null): mixed
    {
        return Site::get($key, $default);
    }
}
