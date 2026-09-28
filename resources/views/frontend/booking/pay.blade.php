<x-site-layout :title="__('Pay for booking :ref', ['ref' => $booking->reference]).' | '.site('name')">
    @push('head')<meta name="robots" content="noindex, nofollow">@endpush

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ \App\Services\BookingService::confirmationUrl($booking) }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-700"><x-icon name="chevron-left" class="h-4 w-4" /> {{ __('Back to booking') }}</a>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-[1fr_280px] gap-8">
            <form method="POST" action="{{ URL::signedRoute('booking.pay.start', $booking) }}" class="rt-card p-6 sm:p-8" x-data="{ method: 'esewa' }">
                @csrf
                <h1 class="text-xl font-bold text-brand-950">{{ __('Choose a payment method') }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ __('All payments are charged in Nepali Rupees (NPR).') }}</p>

                @foreach (['nepal' => __('Nepal wallets & banking'), 'international' => __('International cards'), 'offline' => __('Offline')] as $group => $groupLabel)
                    <p class="mt-6 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $groupLabel }}</p>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach (collect($methods)->where('group', $group) as $key => $method)
                            <label class="cursor-pointer">
                                <input type="radio" name="method" value="{{ $key }}" x-model="method" class="peer sr-only">
                                <span class="flex items-center gap-3 rounded-xl border-2 border-slate-200 p-4 transition peer-checked:border-brand-600 peer-checked:bg-brand-50">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-lg text-xs font-extrabold text-white" style="background: {{ $method['color'] }}">{{ Str::upper(Str::substr($method['label'], 0, 2)) }}</span>
                                    <span class="flex-1">
                                        <span class="block text-sm font-semibold text-brand-950">{{ $method['label'] }}</span>
                                        <span class="block text-[11px] text-slate-500">
                                            @if ($key === 'esewa') {{ __('Instant — pay with your eSewa wallet') }}
                                            @elseif ($key === 'khalti') {{ $khaltiEnabled ? __('Instant — Khalti wallet / mobile banking') : __('Our team sends you a Khalti payment link') }}
                                            @elseif ($key === 'office') {{ __('Pay cash / bank transfer at Gangabu office') }}
                                            @else {{ __('Our team sends you a secure payment link / QR') }}
                                            @endif
                                        </span>
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endforeach

                <button class="rt-btn-gold mt-8 w-full"><x-icon name="shield" class="h-4 w-4" /> {{ __('Pay :amount', ['amount' => npr($booking->total)]) }}</button>
                <p class="mt-3 text-center text-xs text-slate-400">{{ __('You will be redirected to the secure payment page of the provider.') }}</p>
            </form>

            <aside class="rt-card h-fit p-6">
                <p class="text-xs uppercase tracking-wide text-slate-400">{{ __('Booking') }} {{ $booking->reference }}</p>
                <h2 class="mt-1 font-semibold text-brand-950">{{ $booking->title }}</h2>
                @if ($booking->travel_date)<p class="mt-1 text-sm text-slate-500">{{ $booking->travel_date->format('D, M d, Y') }}</p>@endif
                <dl class="mt-4 space-y-1.5 border-t border-slate-100 pt-4 text-sm">
                    <div class="flex justify-between text-slate-600"><dt>{{ __('Subtotal') }}</dt><dd>{{ npr($booking->subtotal) }}</dd></div>
                    @if ($booking->discount > 0)<div class="flex justify-between text-emerald-600"><dt>{{ __('Discount') }}</dt><dd>- {{ npr($booking->discount) }}</dd></div>@endif
                    <div class="flex justify-between pt-2 text-lg font-bold text-brand-950"><dt>{{ __('Total') }}</dt><dd>{{ npr($booking->total) }}</dd></div>
                </dl>
            </aside>
        </div>
    </div>
</x-site-layout>
