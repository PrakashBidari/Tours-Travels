@php
    $steps = match ($booking->service_type) {
        'flight', 'bus' => ['pending' => __('Requested'), 'confirmed' => __('Confirmed'), 'ticketed' => __('Ticket Issued'), 'completed' => __('Travelled')],
        'visa' => ['pending' => __('Submitted'), 'processing' => __('Processing'), 'confirmed' => __('Approved'), 'completed' => __('Completed')],
        default => ['pending' => __('Received'), 'confirmed' => __('Confirmed'), 'completed' => __('Completed')],
    };
    $stepKeys = array_keys($steps);
    $currentStep = array_search($booking->status, $stepKeys, true);
    $cancelled = in_array($booking->status, ['cancelled', 'refunded'], true);
@endphp

<x-site-layout :title="__('Booking :ref', ['ref' => $booking->reference]).' | '.site('name')" :canonical="route('booking.track')">
    @push('head')<meta name="robots" content="noindex, nofollow">@endpush

    <section class="bg-gradient-to-br from-brand-800 via-brand-700 to-sky-600 no-print">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center text-white">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full {{ $cancelled ? 'bg-rose-500' : 'bg-emerald-500' }} shadow-lg">
                <x-icon :name="$cancelled ? 'x' : 'check'" class="h-8 w-8" />
            </span>
            <h1 class="mt-4 text-2xl sm:text-3xl font-bold">
                {{ $cancelled ? __('Booking :status', ['status' => ucfirst($booking->status)]) : ($booking->isPaid() ? __('Booking confirmed & paid!') : __('Thank you! Your booking is received.')) }}
            </h1>
            <p class="mt-2 text-brand-100">{{ __('A confirmation has been sent to :email', ['email' => $booking->email]) }}</p>
            <p class="mt-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-5 py-2 text-sm ring-1 ring-white/30">{{ __('Booking ID') }} <strong class="font-mono text-lg tracking-wider text-gold-300">{{ $booking->reference }}</strong></p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Status timeline -->
        @unless ($cancelled)
            <ol class="rt-card mb-8 grid grid-cols-{{ count($steps) }} gap-2 p-5 no-print">
                @foreach ($steps as $key => $label)
                    @php $done = $currentStep !== false && $loop->index <= $currentStep; @endphp
                    <li class="relative text-center">
                        @unless ($loop->last)<span class="absolute left-1/2 top-4 h-0.5 w-full {{ $currentStep !== false && $loop->index < $currentStep ? 'bg-emerald-500' : 'bg-slate-200' }}"></span>@endunless
                        <span class="relative mx-auto flex h-8 w-8 items-center justify-center rounded-full text-xs font-bold {{ $done ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                            @if ($done)<x-icon name="check" class="h-4 w-4" />@else{{ $loop->iteration }}@endif
                        </span>
                        <span class="mt-2 block text-xs font-medium {{ $done ? 'text-brand-950' : 'text-slate-400' }}">{{ $label }}</span>
                    </li>
                @endforeach
            </ol>
        @endunless

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8">
            <div class="rt-card overflow-hidden">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-6 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">{{ $booking->service_label }}</p>
                        <h2 class="text-lg font-bold text-brand-950">{{ $booking->title }}</h2>
                    </div>
                    <div class="flex gap-2">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ \App\Models\ServiceBooking::badgeClasses($booking->status) }}">{{ ucfirst($booking->status) }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ \App\Models\ServiceBooking::badgeClasses($booking->payment_status) }}">{{ __('Payment') }}: {{ ucfirst($booking->payment_status) }}</span>
                    </div>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 p-6 text-sm">
                    <div><dt class="text-xs text-slate-400">{{ __('Lead traveler') }}</dt><dd class="font-medium text-brand-950">{{ $booking->full_name }}</dd></div>
                    <div><dt class="text-xs text-slate-400">{{ __('Contact') }}</dt><dd class="font-medium text-brand-950">{{ $booking->phone }} · {{ $booking->email }}</dd></div>
                    @if ($booking->travel_date)<div><dt class="text-xs text-slate-400">{{ $booking->service_type === 'car' ? __('Pickup date') : __('Travel date') }}</dt><dd class="font-medium text-brand-950">{{ $booking->travel_date->format('D, M d, Y') }}</dd></div>@endif
                    @if ($booking->return_date)<div><dt class="text-xs text-slate-400">{{ __('Return / end date') }}</dt><dd class="font-medium text-brand-950">{{ $booking->return_date->format('D, M d, Y') }}</dd></div>@endif
                    <div><dt class="text-xs text-slate-400">{{ $booking->service_type === 'visa' ? __('Applicants') : __('Travelers') }}</dt><dd class="font-medium text-brand-950">{{ $booking->adults }} {{ __('adult(s)') }}@if ($booking->children), {{ $booking->children }} {{ __('child(ren)') }}@endif</dd></div>
                    @if ($booking->passport_number)<div><dt class="text-xs text-slate-400">{{ __('Passport') }}</dt><dd class="font-medium text-brand-950">{{ Str::mask($booking->passport_number, '•', 2, -2) }}</dd></div>@endif
                    @if ($booking->pnr)<div><dt class="text-xs text-slate-400">PNR</dt><dd class="font-mono font-bold text-brand-700">{{ $booking->pnr }}</dd></div>@endif
                    @if ($booking->ticket_number)<div><dt class="text-xs text-slate-400">{{ __('Ticket number') }}</dt><dd class="font-mono font-bold text-brand-700">{{ $booking->ticket_number }}</dd></div>@endif
                    @include('frontend.booking.partials.details', ['labelClass' => 'text-xs text-slate-400', 'valueClass' => 'font-medium text-brand-950'])
                    @if ($booking->special_request)<div class="sm:col-span-2"><dt class="text-xs text-slate-400">{{ __('Special request') }}</dt><dd class="text-slate-600">{{ $booking->special_request }}</dd></div>@endif
                    @if ($docs = $booking->details['documents'] ?? [])
                        <div class="sm:col-span-2"><dt class="text-xs text-slate-400">{{ __('Documents uploaded') }}</dt><dd class="text-slate-600">{{ count($docs) }} {{ __('file(s)') }} — {{ collect($docs)->pluck('name')->implode(', ') }}</dd></div>
                    @endif
                </dl>

                <div class="border-t border-slate-100 px-6 py-5">
                    @if ($booking->total > 0)
                        <dl class="ml-auto max-w-xs space-y-1.5 text-sm">
                            <div class="flex justify-between text-slate-600"><dt>{{ __('Subtotal') }}</dt><dd>{{ npr($booking->subtotal) }}</dd></div>
                            @if ($booking->discount > 0)<div class="flex justify-between text-emerald-600"><dt>{{ __('Discount') }} @if ($booking->coupon_code)({{ $booking->coupon_code }})@endif</dt><dd>- {{ npr($booking->discount) }}</dd></div>@endif
                            <div class="flex justify-between border-t border-slate-100 pt-2 text-base font-bold text-brand-950"><dt>{{ __('Total') }}</dt><dd>{{ npr($booking->total) }}</dd></div>
                            @if ($booking->isPaid())<div class="flex justify-between text-xs text-emerald-600"><dt>{{ __('Paid via :method', ['method' => $booking->payment_method_label]) }}</dt><dd>{{ $booking->paid_at?->format('M d, Y') }}</dd></div>@endif
                        </dl>
                    @else
                        <p class="rounded-xl bg-sky-50 p-4 text-sm text-sky-800">{{ __('Our team is checking live fares and availability. We will send you the final price to confirm — usually within 30 minutes during office hours.') }}</p>
                    @endif
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rt-card p-5 text-center">
                    <div class="mx-auto w-fit rounded-xl bg-white p-2 ring-1 ring-slate-200 [&_svg]:h-36 [&_svg]:w-36">{!! $qr !!}</div>
                    <p class="mt-2 text-xs text-slate-500">{{ __('Show this QR code at check-in / boarding') }}</p>
                </div>

                <div class="space-y-2 no-print">
                    @if ($booking->isPayable())
                        <a href="{{ $payUrl }}" class="rt-btn-gold w-full"><x-icon name="wallet" class="h-4 w-4" /> {{ __('Pay :amount Online', ['amount' => npr($booking->total)]) }}</a>
                    @endif
                    <a href="{{ $invoiceUrl }}" class="rt-btn-primary w-full"><x-icon name="download" class="h-4 w-4" /> {{ __('Download Invoice') }}</a>
                    <button type="button" onclick="window.print()" class="rt-btn-outline w-full"><x-icon name="printer" class="h-4 w-4" /> {{ __('Print Ticket') }}</button>
                    <a href="https://wa.me/{{ site('whatsapp') }}?text={{ urlencode(__('Hello Ram Tours, my booking ID is :ref', ['ref' => $booking->reference])) }}" target="_blank" rel="noopener" class="rt-btn w-full bg-[#25D366] text-white hover:brightness-95"><x-social-icon platform="whatsapp" class="h-4 w-4" /> {{ __('Chat about this booking') }}</a>
                </div>

                <div class="rounded-2xl bg-slate-100 p-4 text-xs text-slate-600 no-print">
                    <p class="font-semibold text-slate-700">{{ __('Save this page') }}</p>
                    <p class="mt-1">{{ __('Bookmark this page or use Track Booking with your booking ID and email to come back any time.') }}</p>
                </div>
            </aside>
        </div>
    </div>
</x-site-layout>
