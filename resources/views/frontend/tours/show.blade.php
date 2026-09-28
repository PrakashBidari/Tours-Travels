@php
    $images = $tour->image_urls;
    $schema = [[
        '@context' => 'https://schema.org',
        '@type' => 'TouristTrip',
        'name' => $tour->title,
        'description' => $tour->summary,
        'image' => $images,
        'touristType' => $tour->trip_style,
        'itinerary' => ['@type' => 'ItemList', 'numberOfItems' => count($tour->itinerary ?? [])],
        'offers' => [
            '@type' => 'Offer',
            'price' => $tour->final_price,
            'priceCurrency' => 'NPR',
            'availability' => 'https://schema.org/InStock',
            'url' => route('tours.show', $tour),
        ],
        'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => (float) $tour->rating, 'reviewCount' => max(1, $tour->review_count)],
    ]];
    $shareText = urlencode($tour->title.' — '.route('tours.show', $tour));
@endphp

<x-site-layout :title="($tour->meta_title ?: $tour->title).' | '.site('name')" :description="$tour->meta_description ?: $tour->summary" :image="$tour->main_image" :schema="$schema"
    :breadcrumbs="[[__('Tour Packages'), route('tours.index')], [$tour->category_label, route('tours.index', ['category' => $tour->category])], [$tour->title, null]]">

    <div class="bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-8">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-brand-700">{{ __('Home') }}</a>
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                <a href="{{ route('tours.index', ['category' => $tour->category]) }}" class="hover:text-brand-700">{{ $tour->category_label }}</a>
                <x-icon name="chevron-right" class="h-3.5 w-3.5" />
                <span class="text-slate-700">{{ $tour->title }}</span>
            </nav>

            <div class="mt-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <span class="rt-chip">{{ $tour->category_label }}</span>
                    <h1 class="mt-3 text-2xl sm:text-4xl font-bold tracking-tight text-brand-950">{{ $tour->title }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-600">
                        <span class="flex items-center gap-1.5"><x-icon name="map-pin" class="h-4 w-4 text-sky-500" /> {{ $tour->destination }}, {{ $tour->country }}</span>
                        <span class="flex items-center gap-1.5"><x-icon name="clock" class="h-4 w-4 text-sky-500" /> {{ $tour->duration_label }}</span>
                        <span class="flex items-center gap-1"><x-icon name="star" :solid="true" class="h-4 w-4 text-gold-500" /> <strong>{{ number_format((float) $tour->rating, 1) }}</strong> ({{ $tour->review_count }} {{ __('reviews') }})</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 no-print">
                    <button type="button" data-wishlist-tour="{{ $tour->slug }}" data-title="{{ $tour->title }}" class="rt-btn border border-slate-200 px-4 py-2 text-slate-600 hover:border-rose-300 hover:text-rose-500"><x-icon name="heart" class="h-4 w-4" /> {{ __('Save') }}</button>
                    <button type="button" data-compare-tour="{{ $tour->slug }}" data-title="{{ $tour->title }}" class="rt-btn border border-slate-200 px-4 py-2 text-slate-600 hover:border-brand-300 hover:text-brand-700"><x-icon name="swap" class="h-4 w-4" /> {{ __('Compare') }}</button>
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" class="rt-btn border border-slate-200 px-4 py-2 text-slate-600 hover:text-brand-700"><x-icon name="share" class="h-4 w-4" /> {{ __('Share') }}</button>
                        <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 z-30 mt-2 w-48 rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-900/5 text-sm">
                            <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener" class="block rounded-lg px-3 py-2 hover:bg-brand-50">WhatsApp</a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('tours.show', $tour)) }}" target="_blank" rel="noopener" class="block rounded-lg px-3 py-2 hover:bg-brand-50">Facebook</a>
                            <a href="https://twitter.com/intent/tweet?text={{ $shareText }}" target="_blank" rel="noopener" class="block rounded-lg px-3 py-2 hover:bg-brand-50">X / Twitter</a>
                            <button type="button" @click="navigator.clipboard.writeText(@js(route('tours.show', $tour))); AppAlert.success(@js(__('Link copied'))); open = false" class="block w-full rounded-lg px-3 py-2 text-left hover:bg-brand-50">{{ __('Copy link') }}</button>
                        </div>
                    </div>
                    <a href="{{ route('tours.itinerary', $tour) }}" class="rt-btn border border-slate-200 px-4 py-2 text-slate-600 hover:text-brand-700"><x-icon name="download" class="h-4 w-4" /> {{ __('Itinerary PDF') }}</a>
                </div>
            </div>

            <!-- Gallery -->
            <div class="mt-6 grid grid-cols-4 grid-rows-2 gap-2 h-[260px] sm:h-[420px] overflow-hidden rounded-2xl">
                @foreach (array_slice($images, 0, 3) as $i => $url)
                    <a href="{{ $url }}" class="glightbox group relative overflow-hidden {{ $i === 0 ? 'col-span-4 row-span-2 sm:col-span-3' : 'hidden sm:block' }}" data-gallery="tour-{{ $tour->id }}">
                        <img src="{{ $url }}" alt="{{ $tour->title }} — {{ __('photo') }} {{ $i + 1 }}" @if ($i > 0) loading="lazy" @endif class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @if ($i === 0 && count($images) > 1)
                            <span class="absolute bottom-3 right-3 flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-brand-950 sm:hidden"><x-icon name="camera" class="h-4 w-4" /> {{ count($images) }}</span>
                        @endif
                    </a>
                @endforeach
                @foreach (array_slice($images, 3) as $url)
                    <a href="{{ $url }}" class="glightbox hidden" data-gallery="tour-{{ $tour->id }}"></a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-10">
            <div class="space-y-8 min-w-0">
                <!-- Quick facts -->
                <div class="rt-card grid grid-cols-2 sm:grid-cols-4 divide-y sm:divide-y-0 divide-slate-100">
                    @foreach (array_filter([
                        ['clock', __('Duration'), $tour->duration_label],
                        ['sun', __('Best season'), __($tour->season_label)],
                        ['users', __('Group size'), $tour->group_size],
                        $tour->max_altitude ? ['mountain', __('Max altitude'), $tour->max_altitude] : ['badge', __('Trip style'), __(config('travel.trip_styles.'.$tour->trip_style, '—'))],
                        ['hotel', __('Accommodation'), $tour->hotel],
                        ['utensils', __('Meals'), $tour->meals],
                        ['car', __('Transport'), $tour->transport],
                        $tour->difficulty ? ['cog', __('Difficulty'), $tour->difficulty] : null,
                    ]) as [$icon, $factLabel, $value])
                        @continue(! $value)
                        <div class="flex items-start gap-3 p-4">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700"><x-icon :name="$icon" class="h-4 w-4" /></span>
                            <div class="min-w-0"><p class="text-[11px] uppercase tracking-wide text-slate-400">{{ $factLabel }}</p><p class="text-sm font-semibold text-brand-950">{{ $value }}</p></div>
                        </div>
                    @endforeach
                </div>

                <!-- Section nav -->
                <nav class="sticky top-[72px] lg:top-20 z-20 -mx-4 overflow-x-auto bg-slate-50/95 px-4 py-3 backdrop-blur sm:mx-0 sm:rounded-xl sm:bg-white sm:shadow-sm sm:ring-1 sm:ring-slate-900/5 no-print">
                    <div class="flex gap-6 text-sm font-semibold text-slate-500 whitespace-nowrap">
                        <a href="#overview" class="hover:text-brand-700">{{ __('Overview') }}</a>
                        @if ($tour->itinerary)<a href="#itinerary" class="hover:text-brand-700">{{ __('Itinerary') }}</a>@endif
                        <a href="#includes" class="hover:text-brand-700">{{ __('Included / Excluded') }}</a>
                        @if ($tour->visa_info)<a href="#visa" class="hover:text-brand-700">{{ __('Visa Info') }}</a>@endif
                        @if ($tour->map_embed_url)<a href="#map" class="hover:text-brand-700">{{ __('Map') }}</a>@endif
                        <a href="#book" class="text-brand-700">{{ __('Book Now') }}</a>
                    </div>
                </nav>

                <section id="overview" class="scroll-mt-40">
                    <h2 class="text-xl font-bold text-brand-950">{{ __('Overview') }}</h2>
                    <div class="rt-prose mt-3">{!! clean($tour->overview ?: '<p>'.e($tour->summary).'</p>') !!}</div>

                    @if ($tour->highlights)
                        <h3 class="mt-6 font-semibold text-brand-950">{{ __('Trip highlights') }}</h3>
                        <ul class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($tour->highlights as $highlight)
                                <li class="flex items-start gap-2 text-sm text-slate-600"><x-icon name="check-circle" class="h-5 w-5 shrink-0 text-emerald-500" /> {{ $highlight }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                @if ($tour->itinerary)
                    <section id="itinerary" class="scroll-mt-40" x-data="{ open: 0, all: false }">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-brand-950">{{ __('Day-by-day itinerary') }}</h2>
                            <button type="button" @click="all = !all" class="text-sm font-semibold text-brand-700 no-print" x-text="all ? @js(__('Collapse all')) : @js(__('Expand all'))"></button>
                        </div>
                        <ol class="mt-4 relative border-l-2 border-dashed border-brand-200 ml-4 space-y-3">
                            @foreach ($tour->itinerary as $i => $day)
                                <li class="relative pl-8">
                                    <span class="absolute -left-[17px] top-3 flex h-8 w-8 items-center justify-center rounded-full bg-brand-700 text-xs font-bold text-white ring-4 ring-white">{{ $i + 1 }}</span>
                                    <div class="rt-card overflow-hidden">
                                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" class="flex w-full items-center justify-between gap-3 p-4 text-left">
                                            <span><span class="text-xs font-semibold uppercase text-sky-600">{{ __('Day') }} {{ $i + 1 }}</span><span class="block font-semibold text-brand-950">{{ $day['title'] ?? '' }}</span></span>
                                            <x-icon name="chevron-down" class="h-5 w-5 shrink-0 text-slate-400 transition" ::class="(all || open === {{ $i }}) && 'rotate-180'" />
                                        </button>
                                        <div x-show="all || open === {{ $i }}" x-collapse class="px-4 pb-4 text-sm leading-relaxed text-slate-600">{{ $day['description'] ?? '' }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endif

                <section id="includes" class="scroll-mt-40 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="rt-card p-5">
                        <h2 class="font-bold text-brand-950">{{ __('What’s included') }}</h2>
                        <ul class="mt-3 space-y-2">
                            @foreach ($tour->includes ?? [] as $item)
                                <li class="flex items-start gap-2 text-sm text-slate-600"><x-icon name="check" class="h-5 w-5 shrink-0 text-emerald-500" /> {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="rt-card p-5">
                        <h2 class="font-bold text-brand-950">{{ __('Not included') }}</h2>
                        <ul class="mt-3 space-y-2">
                            @foreach ($tour->excludes ?? [] as $item)
                                <li class="flex items-start gap-2 text-sm text-slate-600"><x-icon name="x" class="h-5 w-5 shrink-0 text-rose-500" /> {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </section>

                @if ($tour->visa_info)
                    <section id="visa" class="scroll-mt-40 rounded-2xl bg-sky-50 p-5 ring-1 ring-sky-100">
                        <h2 class="flex items-center gap-2 font-bold text-brand-950"><x-icon name="visa" class="h-5 w-5 text-sky-600" /> {{ __('Visa information') }}</h2>
                        <p class="mt-2 text-sm text-slate-600">{{ $tour->visa_info }}</p>
                        <a href="{{ route('visa.index', ['country' => $tour->country]) }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-700">{{ __('See visa requirements') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                    </section>
                @endif

                @if ($tour->map_embed_url)
                    <section id="map" class="scroll-mt-40">
                        <h2 class="text-xl font-bold text-brand-950">{{ __('Destination map') }}</h2>
                        <iframe src="{{ $tour->map_embed_url }}" class="mt-4 h-80 w-full rounded-2xl border-0 shadow-card" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ __('Map of :place', ['place' => $tour->destination]) }}"></iframe>
                    </section>
                @endif
            </div>

            <!-- Booking card -->
            <aside id="book" class="scroll-mt-28">
                <div class="rt-card overflow-hidden lg:sticky lg:top-28"
                    x-data="{ adults: {{ (int) old('adults', request('travelers', 2)) }}, children: {{ (int) old('children', 0) }}, price: {{ $tour->final_price }}, discount: 0,
                        get subtotal() { return this.price * this.adults + this.price * 0.5 * this.children },
                        get total() { return Math.max(0, this.subtotal - this.discount) },
                        fmt(v) { return 'Rs. ' + Math.round(v).toLocaleString('en-IN') } }">
                    <div class="bg-gradient-to-r from-brand-700 to-sky-500 p-5 text-white">
                        <p class="text-xs text-white/80">{{ __('Price per person from') }}</p>
                        <p class="text-3xl font-extrabold">{{ npr($tour->final_price) }}
                            @if ($tour->discount_percent)<span class="ml-1 text-base font-normal line-through text-white/70">{{ npr($tour->price) }}</span>@endif
                        </p>
                        @if (session('currency', 'NPR') !== 'NPR')<p class="text-xs text-white/80">≈ {{ money($tour->final_price) }}</p>@endif
                        @if ($tour->discount_percent)<span class="mt-2 inline-block rounded-full bg-gold-500 px-2.5 py-0.5 text-xs font-bold text-brand-950">{{ __('Save :percent%', ['percent' => $tour->discount_percent]) }}</span>@endif
                    </div>

                    <form method="POST" action="{{ route('tours.book', $tour) }}" class="space-y-4 p-5">
                        @csrf
                        <div>
                            <label for="travel_date" class="rt-label">{{ __('Travel date') }} <span class="text-rose-500">*</span></label>
                            <input id="travel_date" type="date" name="travel_date" min="{{ now()->toDateString() }}" value="{{ old('travel_date', request('date')) }}" required class="rt-input">
                            <x-input-error for="travel_date" class="mt-1" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="adults" class="rt-label">{{ __('Adults') }}</label>
                                <input id="adults" type="number" name="adults" min="1" max="50" x-model.number="adults" required class="rt-input">
                            </div>
                            <div>
                                <label for="children" class="rt-label">{{ __('Children (2–11)') }}</label>
                                <input id="children" type="number" name="children" min="0" max="50" x-model.number="children" class="rt-input">
                            </div>
                        </div>

                        <details class="group rounded-xl bg-slate-50 p-4" open>
                            <summary class="flex cursor-pointer list-none items-center justify-between text-sm font-semibold text-brand-950">{{ __('Traveler details') }} <x-icon name="chevron-down" class="h-4 w-4 transition group-open:rotate-180" /></summary>
                            <div class="mt-4">
                                <x-frontend.traveler-fields :passport="$tour->category === 'international'" :passport-required="$tour->category === 'international'" service="tour" amount-expression="subtotal" />
                            </div>
                        </details>

                        <dl class="space-y-1.5 border-t border-slate-100 pt-4 text-sm">
                            <div class="flex justify-between text-slate-600"><dt>{{ __('Adults') }} × <span x-text="adults"></span></dt><dd x-text="fmt(price * adults)"></dd></div>
                            <div class="flex justify-between text-slate-600" x-show="children > 0"><dt>{{ __('Children') }} × <span x-text="children"></span> (50%)</dt><dd x-text="fmt(price * 0.5 * children)"></dd></div>
                            <div class="flex justify-between text-emerald-600" x-show="discount > 0" x-cloak><dt>{{ __('Coupon discount') }}</dt><dd x-text="'- ' + fmt(discount)"></dd></div>
                            <div class="flex justify-between pt-2 text-base font-bold text-brand-950"><dt>{{ __('Total') }}</dt><dd x-text="fmt(total)"></dd></div>
                        </dl>

                        <button class="rt-btn-gold w-full">{{ __('Book Now') }} <x-icon name="arrow-right" class="h-4 w-4" /></button>
                        <p class="text-center text-xs text-slate-400">{{ __('No payment needed now — pay online after we confirm availability.') }}</p>
                    </form>

                    <div class="border-t border-slate-100 p-5 text-center text-sm">
                        <p class="text-slate-500">{{ __('Need help or a custom quote?') }}</p>
                        <div class="mt-2 flex justify-center gap-4 font-semibold">
                            <a href="{{ site('phone_href') }}" class="text-brand-700">{{ site('phone') }}</a>
                            <a href="https://wa.me/{{ site('whatsapp') }}?text={{ urlencode(__('Hi, I am interested in :tour', ['tour' => $tour->title])) }}" target="_blank" rel="noopener" class="text-emerald-600">WhatsApp</a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-16">
                <x-frontend.section-heading :eyebrow="__('You may also like')" :title="__('Similar Packages')" />
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($related as $item)
                        <x-frontend.tour-card :tour="$item" />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-16 no-print" data-recently-viewed data-exclude="{{ $tour->slug }}" hidden>
            <x-frontend.section-heading :eyebrow="__('Your history')" :title="__('Recently Viewed')" />
            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4" data-recently-viewed-list></div>
        </section>
    </div>

    <script type="application/json" data-recent-tour>{!! json_encode(['slug' => $tour->slug, 'title' => $tour->title, 'image' => $tour->main_image, 'price' => npr($tour->final_price), 'url' => route('tours.show', $tour)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
</x-site-layout>
