@php
    $domesticCodes = array_slice(array_keys($airports), 0, 12);
    $defaults = [
        'trip_type' => old('trip_type', $search['trip_type'] ?? 'oneway'),
        'from' => old('from', $search['from'] ?? 'KTM'),
        'to' => old('to', $search['to'] ?? ($scope === 'domestic' ? 'PKR' : 'DXB')),
    ];
    $popularRoutes = [
        ['KTM', 'PKR', 'Buddha Air · Yeti · Shree'], ['KTM', 'BWA', 'Buddha Air · Shree'], ['KTM', 'LUA', 'Tara Air · Summit Air'],
        ['KTM', 'DXB', 'flydubai · Himalaya · Nepal Airlines'], ['KTM', 'DOH', 'Qatar Airways'], ['KTM', 'KUL', 'Malaysia Airlines · Himalaya'],
        ['KTM', 'BKK', 'Thai Airways'], ['KTM', 'DEL', 'Air India · Nepal Airlines'],
    ];
@endphp

<x-site-layout :title="__('Flight Booking — Domestic & International Air Tickets').' | '.site('name')"
    :description="__('Book domestic flights (Buddha Air, Yeti, Shree) and international air tickets (Qatar, Emirates, flydubai, Thai & more) at the best fares from Kathmandu.')"
    :image="config('travel.images.plane_landing')" :breadcrumbs="[[__('Flight Booking'), null]]">

    <x-frontend.page-hero :title="__('Flight Booking')" :eyebrow="__('Fly anywhere')" :image="config('travel.images.plane_landing')"
        :subtitle="__('Domestic & international air tickets at the best fares — our IATA-accredited ticketing desk checks every airline for you.')" :breadcrumbs="[[__('Flight Booking'), null]]">
        <div class="mt-6 flex flex-wrap gap-2">
            @foreach ([null => __('All Flights'), 'domestic' => __('Domestic Flights'), 'international' => __('International Flights')] as $value => $label)
                <a href="{{ route('flights.index', $value ? ['scope' => $value] : []) }}" class="rounded-full px-5 py-2 text-sm font-semibold transition {{ $scope === ($value ?: null) ? 'bg-gold-500 text-brand-950' : 'bg-white/10 text-white ring-1 ring-white/30 hover:bg-white/20' }}">{{ $label }}</a>
            @endforeach
        </div>
    </x-frontend.page-hero>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-8">
            <!-- Booking request form -->
            <form method="POST" action="{{ route('flights.book') }}" class="rt-card p-6 sm:p-8" x-data="{ trip: @js($defaults['trip_type']) }">
                @csrf
                <h2 class="text-xl font-bold text-brand-950">{{ __('Flight booking request') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('Tell us your trip — we reply with the best available fares, usually within 30 minutes during office hours.') }}</p>

                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach (['oneway' => __('One Way'), 'round' => __('Round Trip'), 'multi' => __('Multi City')] as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="trip_type" value="{{ $value }}" x-model="trip" class="peer sr-only">
                            <span class="block rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 peer-checked:border-brand-700 peer-checked:bg-brand-700 peer-checked:text-white">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach (['from' => __('From'), 'to' => __('To')] as $name => $label)
                        <div>
                            <label for="fl-{{ $name }}" class="rt-label">{{ $label }}</label>
                            <select id="fl-{{ $name }}" name="{{ $name }}" class="rt-input">
                                <optgroup label="{{ __('Nepal (domestic)') }}">
                                    @foreach ($domesticCodes as $code)
                                        <option value="{{ $code }}" @selected($defaults[$name] === $code)>{{ $airports[$code] }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('International') }}">
                                    @foreach (array_diff_key($airports, array_flip($domesticCodes)) as $code => $airport)
                                        <option value="{{ $code }}" @selected($defaults[$name] === $code)>{{ $airport }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <x-input-error :for="$name" class="mt-1" />
                        </div>
                    @endforeach
                    <div>
                        <label for="fl-depart" class="rt-label">{{ __('Departure date') }}</label>
                        <input id="fl-depart" type="date" name="depart" min="{{ now()->toDateString() }}" value="{{ old('depart', $search['depart'] ?? '') }}" required class="rt-input">
                        <x-input-error for="depart" class="mt-1" />
                    </div>
                    <div x-show="trip === 'round'" x-cloak>
                        <label for="fl-return" class="rt-label">{{ __('Return date') }}</label>
                        <input id="fl-return" type="date" name="return" min="{{ now()->toDateString() }}" value="{{ old('return', $search['return'] ?? '') }}" :required="trip === 'round'" class="rt-input">
                        <x-input-error for="return" class="mt-1" />
                    </div>
                    <div x-show="trip === 'multi'" x-cloak class="sm:col-span-2">
                        <label for="fl-multi" class="rt-label">{{ __('Other legs (city & date for each)') }}</label>
                        <textarea id="fl-multi" name="multi_city_notes" rows="2" placeholder="{{ __('e.g. DXB → IST on 12 Nov, IST → KTM on 20 Nov') }}" class="rt-input">{{ old('multi_city_notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label for="fl-adults" class="rt-label">{{ __('Adults (12+)') }}</label>
                        <select id="fl-adults" name="adults" class="rt-input">@foreach (range(1, 9) as $n)<option value="{{ $n }}" @selected((int) old('adults', $search['adults'] ?? 1) === $n)>{{ $n }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label for="fl-children" class="rt-label">{{ __('Children (2–11)') }}</label>
                        <select id="fl-children" name="children" class="rt-input">@foreach (range(0, 9) as $n)<option value="{{ $n }}" @selected((int) old('children', 0) === $n)>{{ $n }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label for="fl-infants" class="rt-label">{{ __('Infants (<2)') }}</label>
                        <select id="fl-infants" name="infants" class="rt-input">@foreach (range(0, 4) as $n)<option value="{{ $n }}">{{ $n }}</option>@endforeach</select>
                    </div>
                    <div>
                        <label for="fl-cabin" class="rt-label">{{ __('Cabin') }}</label>
                        <select id="fl-cabin" name="cabin" class="rt-input">
                            @foreach (['economy' => __('Economy'), 'premium' => __('Premium Economy'), 'business' => __('Business'), 'first' => __('First')] as $value => $label)
                                <option value="{{ $value }}" @selected(old('cabin', $search['cabin'] ?? 'economy') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="fl-airline" class="rt-label">{{ __('Preferred airline (optional)') }}</label>
                        <select id="fl-airline" name="airline" class="rt-input">
                            <option value="">{{ __('Any airline — cheapest fare') }}</option>
                            @foreach (array_unique(array_merge(config('travel.airlines.domestic'), config('travel.airlines.international'))) as $airline)
                                <option value="{{ $airline }}" @selected(old('airline') === $airline)>{{ $airline }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label for="fl-promo" class="rt-label">{{ __('Promo code (optional)') }}</label>
                        <input id="fl-promo" name="coupon_code" value="{{ old('coupon_code') }}" class="rt-input uppercase">
                    </div>
                </div>

                <h3 class="mt-8 border-t border-slate-100 pt-6 font-semibold text-brand-950">{{ __('Lead passenger') }}</h3>
                <div class="mt-4">
                    <x-frontend.traveler-fields :passport="true" />
                </div>

                <div class="mt-6 flex flex-col sm:flex-row sm:items-center gap-4">
                    <button class="rt-btn-gold"><x-icon name="plane" class="h-4 w-4" /> {{ __('Request Best Fare') }}</button>
                    <p class="text-xs text-slate-400">{{ __('No payment now. We confirm the fare first, then you pay online to issue your e-ticket.') }}</p>
                </div>
            </form>

            <aside class="space-y-6">
                <div class="rt-card p-6">
                    <h3 class="font-semibold text-brand-950">{{ __('Why book flights with us?') }}</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        @foreach ([__('IATA-accredited ticketing desk'), __('Compare every airline in one request'), __('Student, labour & group fares'), __('Free date-change assistance'), __('Pay with eSewa, Khalti, card or cash')] as $point)
                            <li class="flex gap-2"><x-icon name="check-circle" class="h-5 w-5 shrink-0 text-emerald-500" /> {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="rt-card p-6">
                    <h3 class="font-semibold text-brand-950">{{ __('Popular routes') }}</h3>
                    <ul class="mt-3 divide-y divide-slate-100">
                        @foreach ($popularRoutes as [$from, $to, $carriers])
                            <li>
                                <a href="{{ route('flights.index', ['from' => $from, 'to' => $to, 'scope' => in_array($to, $domesticCodes, true) ? 'domestic' : 'international']) }}" class="flex items-center justify-between gap-3 py-2.5 text-sm hover:text-brand-700">
                                    <span><span class="font-semibold text-brand-950">{{ $from }} → {{ $to }}</span><span class="block text-xs text-slate-400">{{ $carriers }}</span></span>
                                    <x-icon name="chevron-right" class="h-4 w-4 text-slate-300" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl bg-brand-700 p-6 text-white">
                    <p class="text-sm text-brand-100">{{ __('Prefer to talk?') }}</p>
                    <p class="mt-1 text-lg font-bold">{{ __('Ticketing hotline') }}</p>
                    <a href="{{ site('phone_href') }}" class="mt-2 block text-2xl font-extrabold text-gold-400">{{ site('phone') }}</a>
                    <a href="https://wa.me/{{ site('whatsapp') }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold"><x-social-icon platform="whatsapp" class="h-4 w-4" /> {{ site('mobile') }}</a>
                </div>
            </aside>
        </div>

        <!-- Airlines -->
        <section class="mt-16">
            <x-frontend.section-heading :eyebrow="__('Popular Airlines')" :title="__('We ticket every major airline')" />
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach (['domestic' => __('Domestic airlines'), 'international' => __('International airlines')] as $key => $label)
                    <div class="rt-card p-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ $label }}</h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach (config('travel.airlines.'.$key) as $airline)
                                <span class="flex items-center gap-2 rounded-full bg-brand-50 px-4 py-2 text-sm font-medium text-brand-900"><x-icon name="plane" class="h-4 w-4 text-sky-500" /> {{ $airline }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- After-sales services -->
        <section id="services" class="mt-16 scroll-mt-28" x-data="{ type: @js(old('type', 'pnr')) }">
            <x-frontend.section-heading :eyebrow="__('Ticket Services')" :title="__('PNR, Fare, Reissue & Refund Requests')" :subtitle="__('Already have a ticket? Send us a request and our ticketing staff will handle it.')" />
            <div class="mt-6 grid grid-cols-1 lg:grid-cols-[300px_1fr] gap-6">
                <div class="grid grid-cols-2 lg:grid-cols-1 gap-3">
                    @foreach (['pnr' => ['search', __('PNR Inquiry'), __('Check status of a booking')], 'fare' => ['tag', __('Fare Inquiry'), __('Get a fare quote')], 'reissue' => ['calendar', __('Ticket Reissue'), __('Change date or route')], 'refund' => ['wallet', __('Refund Request'), __('Cancel and claim a refund')]] as $key => [$icon, $label, $hint])
                        <button type="button" @click="type = '{{ $key }}'" :class="type === '{{ $key }}' ? 'bg-brand-700 text-white shadow-lg' : 'bg-white text-slate-700 ring-1 ring-slate-200 hover:ring-brand-300'" class="flex items-center gap-3 rounded-xl p-4 text-left transition">
                            <x-icon :name="$icon" class="h-6 w-6 shrink-0" />
                            <span><span class="block text-sm font-semibold">{{ $label }}</span><span class="block text-xs opacity-75">{{ $hint }}</span></span>
                        </button>
                    @endforeach
                </div>
                <form method="POST" action="{{ route('flights.inquiry') }}" class="rt-card grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">
                    @csrf
                    <input type="hidden" name="type" :value="type">
                    <div><label for="iq-name" class="rt-label">{{ __('Full name') }}</label><input id="iq-name" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="rt-input"></div>
                    <div><label for="iq-phone" class="rt-label">{{ __('Phone') }}</label><input id="iq-phone" name="phone" value="{{ old('phone') }}" required class="rt-input"></div>
                    <div><label for="iq-email" class="rt-label">{{ __('Email') }}</label><input id="iq-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="rt-input"></div>
                    <div>
                        <label for="iq-pnr" class="rt-label">{{ __('PNR / booking reference') }} <span x-show="type !== 'fare'" class="text-rose-500">*</span></label>
                        <input id="iq-pnr" name="pnr" value="{{ old('pnr') }}" :required="type !== 'fare'" class="rt-input uppercase">
                        <x-input-error for="pnr" class="mt-1" />
                    </div>
                    <div class="sm:col-span-2"><label for="iq-airline" class="rt-label">{{ __('Airline') }}</label><input id="iq-airline" name="airline" value="{{ old('airline') }}" class="rt-input"></div>
                    <div class="sm:col-span-2"><label for="iq-message" class="rt-label">{{ __('Details') }}</label><textarea id="iq-message" name="message" rows="3" class="rt-input" :placeholder="type === 'reissue' ? @js(__('New date / route you want')) : (type === 'refund' ? @js(__('Reason for cancellation')) : @js(__('Anything we should know')))">{{ old('message') }}</textarea></div>
                    <div class="sm:col-span-2"><button class="rt-btn-primary">{{ __('Submit Request') }}</button></div>
                </form>
            </div>
        </section>
    </div>
</x-site-layout>
