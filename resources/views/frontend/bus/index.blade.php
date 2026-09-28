<x-site-layout :title="__('Bus Ticket Booking — Tourist, Deluxe & Sofa Buses').' | '.site('name')"
    :description="__('Book Kathmandu–Pokhara, Chitwan, Lumbini, Butwal, Dharan and Nepalgunj tourist, deluxe, VIP sofa and night bus tickets online with seat selection.')"
    :image="config('travel.images.bus')" :breadcrumbs="[[__('Bus Tickets'), null]]">

    <x-frontend.page-hero :title="__('Bus Ticket Booking')" :eyebrow="__('Travel Nepal by road')" :image="config('travel.images.bus')"
        :subtitle="__('Tourist, deluxe, AC, VIP sofa and night buses across Nepal — pick your own seat and pay online.')" :breadcrumbs="[[__('Bus Tickets'), null]]" />

    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
        @include('frontend.bus.partials.search-form')
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <x-frontend.section-heading :eyebrow="__('Popular routes')" :title="__('Top Tourist Bus Routes')" />
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($popular as $route)
                <a href="{{ route('bus.search', ['from' => $route->from_city, 'to' => $route->to_city, 'date' => now()->addDay()->toDateString()]) }}" class="rt-card group flex items-center gap-4 p-5 transition hover:-translate-y-0.5 hover:shadow-glow">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700 group-hover:bg-brand-700 group-hover:text-white transition"><x-icon name="bus" class="h-6 w-6" /></span>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-brand-950">{{ $route->from_city }} <span class="text-gold-500">⇄</span> {{ $route->to_city }}</p>
                        <p class="text-xs text-slate-500">{{ $route->buses }} {{ Str::plural(__('bus'), $route->buses) }} {{ __('daily') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-slate-400">{{ __('From') }}</p>
                        <p class="font-bold text-brand-700">{{ money($route->min_price) }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['sofa', __('Luxury Sofa Bus'), __('2+1 reclining sofa seats, lunch, Wi-Fi and charging points on Kathmandu–Pokhara.')],
                ['night', __('Night & VIP Bus'), __('Overnight comfort to Lumbini, Dharan, Nepalgunj and Biratnagar with blankets and pushback seats.')],
                ['tourist', __('Tourist & Deluxe Bus'), __('Budget-friendly daily departures from Kantipath with scenic stops for meals.')],
            ] as [$type, $title, $text])
                <div class="rt-card p-6">
                    <span class="rt-chip">{{ \App\Models\BusRoute::TYPES[$type] ?? $title }}</span>
                    <h3 class="mt-3 font-semibold text-brand-950">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $text }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-12 rounded-3xl bg-gradient-to-r from-brand-700 to-sky-500 p-8 text-white sm:flex sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold">{{ __('Group, school or pilgrimage trip?') }}</h2>
                <p class="mt-1 text-brand-100">{{ __('Reserve a whole bus or Hiace with driver for your group.') }}</p>
            </div>
            <a href="{{ route('cars.index', ['category' => 'coaster']) }}" class="rt-btn-gold mt-4 sm:mt-0">{{ __('Hire a Bus / Coaster') }}</a>
        </div>
    </div>
</x-site-layout>
