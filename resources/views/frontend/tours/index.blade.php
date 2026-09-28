@php
    $categories = config('travel.tour_categories');
    $heading = $categories[$category] ?? __('Tour Packages');
    $heroImage = match ($category) {
        'international' => config('travel.images.maldives'),
        'trekking' => config('travel.images.ama_dablam'),
        'adventure' => config('travel.images.rafting'),
        default => config('travel.images.pokhara'),
    };
    $subtitle = match ($category) {
        'international' => __('Dubai, Thailand, Bali, Singapore, Maldives, Europe and more — with visa assistance included.'),
        'trekking' => __('Everest, Annapurna, Langtang, Manaslu and Mardi Himal treks with licensed guides and porters.'),
        'adventure' => __('Helicopter tours, paragliding, rafting, bungee and jungle safari.'),
        'nepal' => __('Kathmandu, Pokhara, Chitwan, Lumbini, Mustang, Rara and beyond.'),
        default => __('Nepal tours, international holidays, treks and adventure activities.'),
    };
@endphp

<x-site-layout :title="$heading.' | '.site('name')" :description="$subtitle" :image="$heroImage" :breadcrumbs="[[$heading, null]]">
    <x-frontend.page-hero :title="request('destination') ? __(':place Tour Packages', ['place' => request('destination')]) : $heading" :subtitle="$subtitle" :image="$heroImage" :breadcrumbs="[[__('Tour Packages'), route('tours.index')], ...($category ? [[$heading, null]] : [])]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ filtersOpen: false }">
        <!-- Category tabs -->
        <div class="flex gap-2 overflow-x-auto pb-2">
            <a href="{{ route('tours.index', request()->except(['category', 'destination', 'page'])) }}" class="shrink-0 rounded-full px-5 py-2 text-sm font-semibold transition {{ ! $category ? 'bg-brand-700 text-white shadow-md' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-700' }}">{{ __('All Packages') }}</a>
            @foreach ($categories as $key => $label)
                <a href="{{ route('tours.index', ['category' => $key]) }}" class="shrink-0 rounded-full px-5 py-2 text-sm font-semibold transition {{ $category === $key ? 'bg-brand-700 text-white shadow-md' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-700' }}">{{ __($label) }}</a>
            @endforeach
        </div>

        <div class="mt-6 grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-8">
            <!-- Filters -->
            <aside>
                <button type="button" @click="filtersOpen = !filtersOpen" class="lg:hidden rt-btn-outline w-full py-2.5"><x-icon name="filter" class="h-4 w-4" /> {{ __('Filters') }}</button>

                <form method="GET" action="{{ route('tours.index') }}" :class="filtersOpen ? 'block' : 'hidden lg:block'" class="rt-card mt-4 lg:mt-0 space-y-5 p-5 lg:sticky lg:top-28">
                    @if ($category)<input type="hidden" name="category" value="{{ $category }}">@endif

                    <div>
                        <label for="f-q" class="rt-label">{{ __('Search') }}</label>
                        <input id="f-q" name="q" value="{{ request('q') }}" placeholder="{{ __('Package or place') }}" class="rt-input">
                    </div>

                    <div>
                        <label for="f-destination" class="rt-label">{{ __('Destination') }}</label>
                        <select id="f-destination" name="destination" class="rt-input">
                            <option value="">{{ __('All destinations') }}</option>
                            @foreach ($destinations as $destination)
                                <option value="{{ $destination }}" @selected(request('destination') === $destination)>{{ $destination }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <span class="rt-label">{{ __('Price per person (NPR)') }}</span>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="min_price" min="0" step="1000" value="{{ request('min_price') }}" placeholder="{{ __('Min') }}" aria-label="{{ __('Minimum price') }}" class="rt-input">
                            <input type="number" name="max_price" min="0" step="1000" value="{{ request('max_price') }}" placeholder="{{ __('Max') }}" aria-label="{{ __('Maximum price') }}" class="rt-input">
                        </div>
                    </div>

                    <fieldset>
                        <legend class="rt-label">{{ __('Duration') }}</legend>
                        <div class="space-y-2">
                            @foreach ($durations as $value => $label)
                                <label class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="radio" name="duration" value="{{ $value }}" @checked(request('duration') === $value) class="text-brand-700 focus:ring-brand-500"> {{ __($label) }}
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div>
                        <label for="f-season" class="rt-label">{{ __('Season') }}</label>
                        <select id="f-season" name="season" class="rt-input">
                            <option value="">{{ __('Any season') }}</option>
                            @foreach (config('travel.seasons') as $value => $label)
                                @continue($value === 'all')
                                <option value="{{ $value }}" @selected(request('season') === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <fieldset>
                        <legend class="rt-label">{{ __('Trip style') }}</legend>
                        <div class="flex flex-wrap gap-2">
                            @foreach (config('travel.trip_styles') as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="style" value="{{ $value }}" @checked(request('style') === $value) class="peer sr-only">
                                    <span class="block rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 peer-checked:border-brand-700 peer-checked:bg-brand-700 peer-checked:text-white">{{ __($label) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="flex gap-2 pt-1">
                        <button class="rt-btn-primary flex-1 py-2.5">{{ __('Apply') }}</button>
                        <a href="{{ route('tours.index', $category ? ['category' => $category] : []) }}" class="rt-btn border border-slate-200 py-2.5 text-slate-600 hover:bg-slate-50">{{ __('Reset') }}</a>
                    </div>
                </form>
            </aside>

            <!-- Results -->
            <div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500"><span class="font-semibold text-brand-950">{{ $tours->total() }}</span> {{ Str::plural(__('package'), $tours->total()) }} {{ __('found') }}</p>
                    <form method="GET" action="{{ route('tours.index') }}" class="flex items-center gap-2">
                        @foreach (request()->except(['sort', 'page']) as $key => $value)
                            @if (! is_array($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
                        @endforeach
                        <label for="sort" class="text-sm text-slate-500">{{ __('Sort by') }}</label>
                        <select id="sort" name="sort" onchange="this.form.submit()" class="rt-input w-auto py-2">
                            @foreach (['' => __('Recommended'), 'price_asc' => __('Price: low to high'), 'price_desc' => __('Price: high to low'), 'duration' => __('Duration'), 'rating' => __('Top rated')] as $value => $label)
                                <option value="{{ $value }}" @selected(request('sort', '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if ($tours->isEmpty())
                    <div class="rt-card mt-6 p-12 text-center">
                        <x-icon name="search" class="mx-auto h-10 w-10 text-slate-300" />
                        <h2 class="mt-4 font-semibold text-brand-950">{{ __('No packages match your filters') }}</h2>
                        <p class="mt-1 text-sm text-slate-500">{{ __('Try widening your search, or ask us to design a custom trip for you.') }}</p>
                        <a href="{{ route('contact', ['subject' => 'Custom tour request']) }}" class="rt-btn-gold mt-5">{{ __('Request a custom package') }}</a>
                    </div>
                @else
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach ($tours as $tour)
                            <x-frontend.tour-card :tour="$tour" />
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $tours->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-site-layout>
