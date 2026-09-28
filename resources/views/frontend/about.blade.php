@php
    $founded = config('travel.company.founded');
    $years = $settings->stat_years ?: now()->year - $founded;
    $timeline = [
        [$founded, __('Ram Tours & Travel founded in Gangabu, Kathmandu with domestic air ticketing.')],
        [$founded + 3, __('Launched Nepal tour packages and the first Everest & Annapurna treks.')],
        [$founded + 6, __('Became an IATA-accredited agent for international ticketing.')],
        [$founded + 9, __('Added visa processing, tourist bus ticketing and vehicle rental.')],
        [$founded + 13, __('International holiday packages to Dubai, Thailand, Bali, Maldives and Europe.')],
        [now()->year, __('Online booking & payments for every service — all in one place.')],
    ];
    $team = [
        ['Ram Bahadur Thapa', __('Founder & Managing Director'), 'avatar_4'],
        ['Sunita Karki', __('Head of Ticketing'), 'avatar_1'],
        ['Bikash Gurung', __('Senior Trekking Consultant'), 'avatar_2'],
        ['Anjali Shrestha', __('Visa & Documentation Manager'), 'avatar_3'],
    ];
@endphp

<x-site-layout :title="__('About Us').' | '.site('name')" :description="__('Ram Tours & Travel Pvt. Ltd. — a government-registered travel agency in Gangabu, Kathmandu offering tours, flights, bus tickets, car rental, hotels and visa services.')" :breadcrumbs="[[__('About Us'), null]]">
    <x-frontend.page-hero :title="$settings->about_title ?: __('About Ram Tours & Travel')" :eyebrow="__('Since :year', ['year' => $founded])" :image="config('travel.images.boudhanath')"
        :subtitle="__('Your journey, our commitment — Nepal’s full-service travel partner for tours, tickets and visas.')" :breadcrumbs="[[__('About Us'), null]]" />

    <!-- Intro -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="relative">
            <img src="{{ $settings->about_image_url ?? config('travel.images.group') }}" alt="{{ __('Ram Tours travelers') }}" class="aspect-[4/3] w-full rounded-3xl object-cover shadow-glow">
            <div class="absolute -bottom-6 -right-2 sm:right-6 rounded-2xl bg-gold-500 px-6 py-4 text-brand-950 shadow-xl">
                <p class="text-4xl font-extrabold">{{ $years }}+</p>
                <p class="text-sm font-semibold">{{ __('Years of Experience') }}</p>
            </div>
        </div>
        <div>
            <x-frontend.section-heading :eyebrow="__('Who we are')" :title="__('Company Introduction')" />
            <div class="rt-prose mt-5">{!! clean($settings->about_description ?: '<p>'.e(__('Ram Tours & Travel Pvt. Ltd. is a government-registered travel agency in Pabitra Nagar, Gangabu, Kathmandu.')).'</p>') !!}</div>
            <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="rounded-2xl bg-brand-50 p-4"><p class="text-2xl font-extrabold text-brand-700">{{ number_format(($settings->stat_travelers ?: 25000) / 1000) }}K+</p><p class="text-xs text-slate-500">{{ __('Happy Travelers') }}</p></div>
                <div class="rounded-2xl bg-brand-50 p-4"><p class="text-2xl font-extrabold text-brand-700">{{ $settings->stat_destinations ?: 120 }}+</p><p class="text-xs text-slate-500">{{ __('Destinations') }}</p></div>
                <div class="rounded-2xl bg-brand-50 p-4"><p class="text-2xl font-extrabold text-brand-700">24/7</p><p class="text-xs text-slate-500">{{ __('Support') }}</p></div>
            </div>
        </div>
    </section>

    <!-- Mission & vision -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ([['sparkles', $settings->mission_title ?: __('Our Mission'), $settings->mission_description], ['globe', $settings->vision_title ?: __('Our Vision'), $settings->vision_description]] as [$icon, $title, $text])
                <div class="rt-card p-8">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-700 to-sky-500 text-white"><x-icon :name="$icon" class="h-7 w-7" /></span>
                    <h2 class="mt-5 text-xl font-bold text-brand-950">{{ $title }}</h2>
                    <div class="rt-prose mt-3">{!! clean($text ?: '') !!}</div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Why choose us -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <x-frontend.section-heading :center="true" :eyebrow="__('Why Choose Us')" :title="__('Travel with Nepal’s trusted experts')" />
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ([
                ['users', __('Expert Travel Consultants'), __('Local specialists who have travelled every route we sell.')],
                ['badge', __('Best Price Guarantee'), __('Direct contracts with airlines, hotels and operators.')],
                ['headset', __('24/7 Customer Support'), __('Phone, WhatsApp and emergency support while you travel.')],
                ['shield', __('Safe & Secure Booking'), __('Registered company with secure online payments.')],
            ] as [$icon, $title, $text])
                <div class="rt-card p-6 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-700"><x-icon :name="$icon" class="h-7 w-7" /></span>
                    <h3 class="mt-4 font-semibold text-brand-950">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Timeline -->
    <section class="bg-brand-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <x-frontend.section-heading :light="true" :center="true" :eyebrow="__('Our journey')" :title="__('Company Timeline')" />
            <ol class="mt-12 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach ($timeline as [$year, $event])
                    <li class="relative rounded-2xl bg-white/5 p-5 ring-1 ring-white/10">
                        <span class="text-2xl font-extrabold text-gold-400">{{ $year }}</span>
                        <p class="mt-2 text-sm text-brand-100">{{ $event }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <!-- Team -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <x-frontend.section-heading :center="true" :eyebrow="__('Meet the team')" :title="__('The people behind your journey')" />
        <div class="mt-10 grid grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($team as [$name, $role, $img])
                <div class="rt-card overflow-hidden text-center">
                    <img src="{{ config('travel.images.'.$img) }}" alt="{{ $name }}" loading="lazy" class="aspect-square w-full object-cover">
                    <div class="p-4"><p class="font-semibold text-brand-950">{{ $name }}</p><p class="text-xs text-slate-500">{{ $role }}</p></div>
                </div>
            @endforeach
        </div>
        <p class="mt-4 text-center text-xs text-slate-400">{{ __('Team photos and names are placeholders — replace with your own.') }}</p>
    </section>

    <!-- Registration & affiliations -->
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 lg:grid-cols-[1fr_1.4fr] gap-10 items-center">
            <div>
                <x-frontend.section-heading :eyebrow="__('Trust & compliance')" :title="__('Registrations & Affiliations')" />
                <p class="mt-4 text-sm text-slate-600">{{ __('Ram Tours & Travel Pvt. Ltd. is registered with the Government of Nepal and affiliated with Nepal’s leading tourism bodies.') }}</p>
                <p class="mt-4 rounded-xl bg-white p-4 text-sm font-medium text-brand-950 ring-1 ring-slate-200">{{ config('travel.company.registration') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @foreach (config('travel.company.affiliations') as $affiliation)
                    <div class="rt-card flex items-center gap-3 p-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gold-100 text-gold-700"><x-icon name="badge" class="h-6 w-6" /></span>
                        <span class="text-sm font-semibold text-brand-950">{{ $affiliation }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Office photos -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <x-frontend.section-heading :eyebrow="__('Visit us')" :title="__('Our Office in Gangabu')" :link="route('contact')" :link-label="__('Get directions')" />
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach (['kathmandu', 'boudhanath', 'group', 'swayambhu'] as $img)
                <img src="{{ config('travel.images.'.$img) }}" alt="" loading="lazy" class="aspect-[4/3] w-full rounded-2xl object-cover">
            @endforeach
        </div>
    </section>
</x-site-layout>
