<x-site-layout :title="__('Contact Us').' | '.site('name')" :description="__('Visit or contact Ram Tours & Travel in Pabitra Nagar, Gangabu, Kathmandu. Call, WhatsApp, Messenger or send an inquiry.')" :breadcrumbs="[[__('Contact Us'), null]]">
    <x-frontend.page-hero :title="__('Contact Us')" :eyebrow="__('We’re here to help')" :image="config('travel.images.kathmandu')"
        :subtitle="__('Questions, quotes or help with a booking — talk to a Ram Tours travel consultant.')" :breadcrumbs="[[__('Contact Us'), null]]" />

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['phone', __('Call Us'), site('phone'), site('phone_href'), null],
                ['phone', __('WhatsApp'), site('mobile'), 'https://wa.me/'.site('whatsapp'), '_blank'],
                ['mail', __('Email'), site('email'), 'mailto:'.site('email'), null],
                ['headset', __('Messenger'), __('Chat on Facebook'), site('messenger'), '_blank'],
            ] as [$icon, $label, $value, $href, $target])
                <a href="{{ $href }}" @if ($target) target="_blank" rel="noopener" @endif class="rt-card group p-5 text-center transition hover:-translate-y-1 hover:shadow-glow">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-700 group-hover:bg-brand-700 group-hover:text-white transition"><x-icon :name="$icon" class="h-6 w-6" /></span>
                    <p class="mt-3 text-sm font-semibold text-brand-950">{{ $label }}</p>
                    <p class="mt-0.5 text-xs text-slate-500 break-all">{{ $value }}</p>
                </a>
            @endforeach
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-8">
            <form method="POST" action="{{ route('inquiry.store') }}" class="rt-card p-6 sm:p-8">
                @csrf
                <h2 class="text-xl font-bold text-brand-950">{{ __('Send us an inquiry') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('We usually reply within a few hours during office hours.') }}</p>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label for="c-name" class="rt-label">{{ __('Full name') }} <span class="text-rose-500">*</span></label><input id="c-name" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="rt-input"><x-input-error for="name" class="mt-1" /></div>
                    <div><label for="c-phone" class="rt-label">{{ __('Phone / WhatsApp') }}</label><input id="c-phone" name="phone" value="{{ old('phone') }}" class="rt-input"></div>
                    <div><label for="c-email" class="rt-label">{{ __('Email') }} <span class="text-rose-500">*</span></label><input id="c-email" type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="rt-input"><x-input-error for="email" class="mt-1" /></div>
                    <div>
                        <label for="c-type" class="rt-label">{{ __('I need help with') }}</label>
                        <select id="c-type" name="type" class="rt-input">
                            @foreach (['general' => __('General Inquiry'), 'quote' => __('Tour / Trip Quote'), 'flight' => __('Flight Tickets'), 'bus' => __('Bus Tickets'), 'visa' => __('Visa Services')] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', request('type', str_contains(strtolower((string) request('subject')), 'visa') ? 'visa' : (request('subject') ? 'quote' : 'general'))) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2"><label for="c-subject" class="rt-label">{{ __('Subject') }}</label><input id="c-subject" name="subject" value="{{ old('subject', request('subject')) }}" class="rt-input"></div>
                    <div class="sm:col-span-2"><label for="c-message" class="rt-label">{{ __('Message') }} <span class="text-rose-500">*</span></label><textarea id="c-message" name="message" rows="5" required placeholder="{{ __('Destination, dates, number of travelers, budget…') }}" class="rt-input">{{ old('message') }}</textarea><x-input-error for="message" class="mt-1" /></div>
                </div>
                <button class="rt-btn-gold mt-6">{{ __('Send Inquiry') }} <x-icon name="arrow-right" class="h-4 w-4" /></button>
            </form>

            <div class="space-y-6">
                <div class="rt-card p-6">
                    <h2 class="text-lg font-bold text-brand-950">{{ __('Office details') }}</h2>
                    <ul class="mt-4 space-y-4 text-sm text-slate-600">
                        <li class="flex gap-3"><x-icon name="map-pin" class="h-5 w-5 shrink-0 text-brand-700" /><span><strong class="block text-brand-950">{{ site('name') }}</strong>{{ site('address') }}</span></li>
                        <li class="flex gap-3"><x-icon name="clock" class="h-5 w-5 shrink-0 text-brand-700" /><span><strong class="block text-brand-950">{{ __('Office hours') }}</strong>{{ site('office_hours') }}</span></li>
                        <li class="flex gap-3"><x-icon name="phone" class="h-5 w-5 shrink-0 text-rose-600" /><span><strong class="block text-brand-950">{{ __('24/7 emergency contact (travelers on trip)') }}</strong><a href="tel:{{ preg_replace('/[^\d+]/', '', site('emergency')) }}" class="font-semibold text-rose-600">{{ site('emergency') }}</a></span></li>
                        <li class="flex gap-3"><x-icon name="badge" class="h-5 w-5 shrink-0 text-brand-700" /><span><strong class="block text-brand-950">{{ __('Registration') }}</strong>{{ config('travel.company.registration') }}</span></li>
                    </ul>
                </div>
                <div class="overflow-hidden rounded-2xl shadow-card ring-1 ring-slate-900/5">
                    <iframe src="{{ site('map_embed') }}" class="h-80 w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ __('Office location map') }}"></iframe>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(site('address')) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 bg-white py-3 text-sm font-semibold text-brand-700 hover:bg-brand-50">{{ __('Open in Google Maps') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>
            </div>
        </div>
    </div>
</x-site-layout>
