<x-site-layout>

    {{-- ============ HERO ============ --}}
    <section class="relative isolate overflow-hidden bg-brand-950">
        @if ($pageSettings->hero_video_url)
            <video autoplay muted loop playsinline poster="{{ $pageSettings->hero_image_url }}" class="absolute inset-0 -z-10 h-full w-full object-cover">
                <source src="{{ $pageSettings->hero_video_url }}" type="video/mp4">
            </video>
        @else
            <img src="{{ $pageSettings->hero_image_url }}" alt="{{ __('Himalayas and stupa in Nepal') }}" class="absolute inset-0 -z-10 h-full w-full object-cover" fetchpriority="high">
        @endif
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-brand-950/90 via-brand-900/55 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-40 bg-gradient-to-t from-brand-950/60 to-transparent"></div>

        <!-- Floating airliner (photo cut-out: Unsplash photo-1587019158091, Unsplash License) -->
        <picture class="pointer-events-none absolute right-[3%] top-8 hidden w-[340px] animate-float drop-shadow-[0_25px_25px_rgba(0,0,0,0.35)] lg:block xl:w-[430px]">
            <source srcset="{{ asset('images/hero-plane.webp') }}" type="image/webp">
            <img src="{{ asset('images/hero-plane.png') }}" alt="" width="1000" height="406" class="h-auto w-full">
        </picture>

        <p class="pointer-events-none absolute right-[4%] top-[57%] hidden rotate-[-8deg] text-right font-script text-3xl leading-tight text-white drop-shadow-lg xl:block">
            {{ __('Travel') }}<br><span class="ml-6">{{ __('Explore') }}</span><br>{{ __('Create Memories') }}
            <span class="mt-1 block h-1 w-40 ml-auto rounded-full bg-gold-500"></span>
        </p>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-20 lg:pt-24 pb-44 sm:pb-48">
            <div class="max-w-2xl animate-fade-up">
                <p class="font-script text-3xl sm:text-4xl text-gold-400">{{ __($pageSettings->hero_script ?: 'Explore the World') }}</p>
                <h1 class="mt-2 text-4xl sm:text-6xl font-extrabold leading-[1.05] tracking-tight text-white">
                    {!! nl2br(e(__($pageSettings->hero_title ?: 'Discover New Destinations'))) !!}
                </h1>
                <p class="mt-5 max-w-xl text-base sm:text-lg text-white/90">{{ __($pageSettings->hero_subtitle ?: 'Your trusted travel partner for Nepal and International tours, flights, hotels, buses, cars and more.') }}</p>
                <p class="mt-4 flex items-center gap-2 text-sm font-medium text-white">
                    <x-icon name="map-pin" class="h-5 w-5 text-gold-400" /> {{ site('address') }}
                </p>
            </div>
        </div>
    </section>

    {{-- ============ SEARCH WIDGET ============ --}}
    <div class="relative z-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-36 sm:-mt-40">
        <x-frontend.search-widget :tour-destinations="$tourDestinations" :visa-countries="$visaCountries" />
    </div>

    {{-- ============ SERVICE STRIP ============ --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-y-8 lg:divide-x lg:divide-slate-200">
                @foreach ([
                    ['plane', __('Air Ticketing'), __('Domestic & International'), route('flights.index')],
                    ['bus', __('Bus Ticketing'), __('Tourist & Deluxe Buses'), route('bus.index')],
                    ['car', __('Car Rental'), __('Self Drive & With Driver'), route('cars.index')],
                    ['hotel', __('Hotel Booking'), __('Best Hotels Worldwide'), route('hotels.index')],
                    ['visa', __('Visa Services'), __('Easy & Hassle Free'), route('visa.index')],
                    ['shield', __('Travel Insurance'), __('Travel with Confidence'), route('contact', ['subject' => 'Travel Insurance'])],
                ] as [$icon, $serviceTitle, $serviceSub, $url])
                    <a href="{{ $url }}" class="group flex flex-col items-center px-3 text-center">
                        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-700 ring-8 ring-brand-50/50 transition group-hover:bg-brand-700 group-hover:text-white">
                            <x-icon :name="$icon" class="h-8 w-8" />
                        </span>
                        <span class="mt-4 text-sm font-semibold text-brand-950 group-hover:text-brand-700">{{ $serviceTitle }}</span>
                        <span class="mt-0.5 text-xs text-slate-500">{{ $serviceSub }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ EXPLORE NEPAL ============ --}}
    <section class="bg-gradient-to-b from-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <x-frontend.section-heading :eyebrow="__('Popular Destinations')" :title="__('Explore Nepal’s Beauty')"
                :subtitle="__('From majestic mountains to serene lakes, colorful culture to thrilling adventures.')"
                :link="route('tours.index', ['category' => 'nepal'])" :link-label="__('View All Nepal Packages')" />

            <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($nepalDestinations as $destination)
                    <a href="{{ $destination['url'] }}" class="group overflow-hidden rounded-xl bg-white shadow-card ring-1 ring-slate-900/5 transition hover:-translate-y-1 hover:shadow-glow">
                        <div class="aspect-[4/3] overflow-hidden">
                            <img src="{{ $destination['image'] }}" alt="{{ $destination['name'] }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                        </div>
                        <div class="p-3.5">
                            <h3 class="font-semibold text-brand-950 group-hover:text-brand-700">{{ __($destination['name']) }}</h3>
                            <p class="text-xs text-slate-500">{{ __($destination['tagline']) }}</p>
                            @if ($destination['count'])
                                <p class="mt-1.5 text-[11px] font-medium text-sky-600">{{ $destination['count'] }} {{ Str::plural(__('package'), $destination['count']) }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ INTERNATIONAL BANNER ============ --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
            <div class="relative overflow-hidden rounded-3xl bg-brand-900 shadow-glow">
                <img src="{{ config('travel.images.beach') }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-r from-brand-800 via-brand-700/85 to-sky-600/40"></div>

                <div class="relative grid grid-cols-1 lg:grid-cols-[1.1fr_1.2fr_0.7fr] items-center gap-8 p-7 sm:p-10">
                    <div class="text-white">
                        <h2 class="text-2xl sm:text-3xl font-bold">{{ __('International Tour Packages') }}</h2>
                        <p class="mt-3 max-w-md text-brand-100">{{ __('Discover the world with our exclusive holiday packages to top destinations.') }}</p>
                        <a href="{{ route('tours.index', ['category' => 'international']) }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-gold-500 px-6 py-3 text-sm font-semibold text-brand-950 shadow-lg hover:bg-gold-400 transition">
                            {{ __('Explore International Packages') }} <x-icon name="arrow-right" class="h-4 w-4" />
                        </a>
                    </div>

                    <div class="relative hidden h-56 sm:block">
                        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 400 220" fill="none" aria-hidden="true">
                            <path d="M20 190 C 120 60, 260 250, 380 40" stroke="white" stroke-width="2" stroke-dasharray="6 8" opacity=".7"/>
                        </svg>
                        <x-icon name="plane" :solid="true" class="absolute right-2 top-2 h-8 w-8 -rotate-45 text-white" />
                        @foreach ([['dubai', 'left-[4%] top-6 -rotate-6'], ['europe', 'left-[26%] top-0 rotate-3'], ['thailand', 'left-[48%] top-8 -rotate-3'], ['maldives', 'left-[66%] top-16 rotate-6']] as [$key, $position])
                            <div class="absolute {{ $position }} w-[30%] rounded-md bg-white p-1.5 pb-5 shadow-2xl transition hover:z-10 hover:scale-105">
                                <img src="{{ config('travel.images.'.$key) }}" alt="{{ ucfirst($key) }}" loading="lazy" class="aspect-[3/4] w-full rounded-sm object-cover">
                            </div>
                        @endforeach
                    </div>

                    <ul class="grid grid-cols-2 lg:grid-cols-1 gap-3">
                        @foreach ($internationalDestinations as $country)
                            <li>
                                <a href="{{ route('tours.index', ['category' => 'international', 'destination' => $country]) }}" class="group flex items-center gap-3 text-sm font-medium text-white">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/30 group-hover:bg-gold-500 group-hover:text-brand-950 transition">
                                        <x-icon name="globe" class="h-4 w-4" />
                                    </span>
                                    <span class="group-hover:text-gold-300">{{ $country === 'Europe' ? __('Europe & More') : __($country) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ SERVICE CARDS ============ --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ([
                    ['plane', __('Flight Booking'), __('Book domestic & international flights'), route('flights.index'), 'from-brand-800/95'],
                    ['bus', __('Bus Ticketing'), __('Comfortable & Safe Journey'), route('bus.index'), 'from-brand-950/95'],
                    ['car', __('Car Rental'), __('Choose Your Perfect Ride'), route('cars.index'), 'from-teal-800/95'],
                    ['hotel_room', __('Hotel Booking'), __('Stay at the Best Hotels'), route('hotels.index'), 'from-rose-900/90'],
                    ['visa', __('Visa Services'), __('Your Visa, Our Support'), route('visa.index'), 'from-brand-900/95'],
                    ['trekking', __('Trekking & Adventure'), __('For the Adventure Seekers'), route('tours.index', ['category' => 'trekking']), 'from-emerald-900/95'],
                ] as [$imageKey, $cardTitle, $cardSub, $url, $tint])
                    <a href="{{ $url }}" class="group relative block aspect-[4/5] overflow-hidden rounded-xl shadow-card">
                        <img src="{{ config('travel.images.'.$imageKey) }}" alt="{{ $cardTitle }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t {{ $tint }} via-transparent to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4 text-white">
                            <h3 class="font-semibold">{{ $cardTitle }}</h3>
                            <p class="text-[11px] text-white/80">{{ $cardSub }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ HOT TOUR DEALS ============ --}}
    @if ($hotDeals->isNotEmpty())
        <section class="bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <x-frontend.section-heading :eyebrow="__('Hot Tour Deals')" :title="__('Handpicked Holiday Packages')"
                    :subtitle="__('Best-selling Nepal and international packages with exclusive Ram Tours prices.')"
                    :link="route('tours.index')" :link-label="__('View all packages')" />

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($hotDeals as $tour)
                        <x-frontend.tour-card :tour="$tour" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ OFFERS ============ --}}
    @if ($offers->isNotEmpty())
        <section class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <x-frontend.section-heading :eyebrow="__('Special Offers')" :title="__('Deals You Can’t Miss')" :link="route('offers.index')" :link-label="__('All offers')" />
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($offers as $offer)
                        <a href="{{ $offer->link_url ?: route('offers.index') }}" class="group relative block overflow-hidden rounded-2xl shadow-card">
                            <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" loading="lazy" class="h-52 w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-r from-brand-950/90 via-brand-900/60 to-transparent"></div>
                            <div class="absolute inset-0 flex flex-col justify-center p-6 text-white">
                                @if ($offer->badge)<span class="w-fit rounded-full bg-gold-500 px-3 py-1 text-[11px] font-bold uppercase text-brand-950">{{ $offer->badge }}</span>@endif
                                <p class="mt-3 text-3xl font-extrabold">{{ $offer->discount_label }}</p>
                                <h3 class="mt-1 font-semibold">{{ $offer->title }}</h3>
                                @if ($offer->code)
                                    <p class="mt-3 text-xs text-brand-100">{{ __('Use code') }} <span class="rounded border border-dashed border-gold-400 px-2 py-0.5 font-mono font-bold text-gold-300">{{ $offer->code }}</span></p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ ADVENTURES ============ --}}
    @if ($adventures->isNotEmpty())
        <section class="relative overflow-hidden bg-brand-950">
            <img src="{{ config('travel.images.sunrise') }}" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover opacity-25">
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <x-frontend.section-heading :light="true" :eyebrow="__('Trekking & Adventure')" :title="__('Himalayan Adventures Await')"
                    :subtitle="__('Everest Base Camp, Annapurna, helicopter tours, paragliding, rafting and more.')"
                    :link="route('tours.index', ['category' => 'trekking'])" :link-label="__('All adventures')" />
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($adventures as $tour)
                        <x-frontend.tour-card :tour="$tour" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ WHY CHOOSE US + TESTIMONIALS ============ --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr_1fr] gap-10 items-center">
                <div>
                    <x-frontend.section-heading :eyebrow="__('Why Choose Us')" :title="__($pageSettings->why_title ?: 'Your Trusted Travel Partner')" />
                    <p class="mt-4 text-slate-600 leading-relaxed">{{ $pageSettings->why_description ?: __('At Ram Tours & Travel Pvt. Ltd., we are committed to making your travel dreams come true with reliable service, best prices and personalized support.') }}</p>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        @foreach ([['users', __('Expert Travel Consultants')], ['badge', __('Best Price Guarantee')], ['headset', __('24/7 Customer Support')], ['shield', __('Safe & Secure Booking')]] as [$icon, $feature])
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700"><x-icon :name="$icon" class="h-5 w-5" /></span>
                                <span class="text-sm font-medium text-brand-950">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-3 rounded-2xl bg-brand-50 p-4 text-center">
                        <div><p class="text-2xl font-extrabold text-brand-700">{{ $pageSettings->stat_years ?: 15 }}+</p><p class="text-[11px] text-slate-500">{{ __('Years Experience') }}</p></div>
                        <div><p class="text-2xl font-extrabold text-brand-700">{{ number_format(($pageSettings->stat_travelers ?: 25000) / 1000) }}K+</p><p class="text-[11px] text-slate-500">{{ __('Happy Travelers') }}</p></div>
                        <div><p class="text-2xl font-extrabold text-brand-700">{{ $pageSettings->stat_destinations ?: 120 }}+</p><p class="text-[11px] text-slate-500">{{ __('Destinations') }}</p></div>
                    </div>

                    <a href="{{ route('about') }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-gold-500 px-6 py-2.5 text-sm font-semibold text-brand-950 hover:bg-gold-400 transition">
                        {{ __('About Us') }} <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>

                <div class="relative">
                    <img src="{{ $pageSettings->why_image_url }}" alt="{{ __('Happy traveler in the Himalayas') }}" loading="lazy" class="aspect-[4/5] w-full rounded-3xl object-cover shadow-glow">
                    <div class="absolute -bottom-5 left-5 flex items-center gap-3 rounded-2xl bg-white px-4 py-3 shadow-xl">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gold-500 text-brand-950"><x-icon name="star" :solid="true" class="h-5 w-5" /></span>
                        <div><p class="text-sm font-bold text-brand-950">4.9 / 5</p><p class="text-[11px] text-slate-500">{{ __('Google Reviews') }}</p></div>
                    </div>
                </div>

                <div x-data="{ i: 0, n: {{ max($testimonials->count(), 1) }}, timer: null, start() { this.timer = setInterval(() => this.i = (this.i + 1) % this.n, 6000) } }" x-init="start()" @mouseenter="clearInterval(timer)" @mouseleave="start()">
                    <x-frontend.section-heading :eyebrow="__('Traveler’s Stories')" :title="__('Happy Travelers')" />
                    @if ($testimonials->isNotEmpty())
                        <div class="relative mt-6">
                            <div class="relative min-h-[220px] rounded-2xl bg-white p-6 shadow-card ring-1 ring-slate-900/5">
                                @foreach ($testimonials as $index => $review)
                                    <figure x-show="i === {{ $index }}" @if (! $loop->first) x-cloak @endif x-transition:enter="transition duration-500" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                                        <div class="flex items-center gap-4">
                                            <img src="{{ $review->avatar_url }}" alt="{{ $review->name }}" loading="lazy" class="h-14 w-14 rounded-full object-cover ring-4 ring-brand-50">
                                            <div>
                                                <figcaption class="font-semibold text-brand-950">{{ $review->name }}</figcaption>
                                                <p class="text-xs text-slate-500">{{ $review->location }}</p>
                                                <div class="mt-1 flex">
                                                    @for ($s = 1; $s <= 5; $s++)
                                                        <x-icon name="star" :solid="true" class="h-4 w-4 {{ $s <= $review->rating ? 'text-gold-500' : 'text-slate-200' }}" />
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <blockquote class="mt-4 text-sm leading-relaxed text-slate-600">&ldquo;{{ $review->content }}&rdquo;</blockquote>
                                    </figure>
                                @endforeach
                            </div>
                            <button type="button" @click="i = (i - 1 + n) % n" class="absolute -left-4 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white text-brand-700 shadow-md hover:bg-brand-50" aria-label="{{ __('Previous') }}"><x-icon name="chevron-left" class="h-4 w-4" /></button>
                            <button type="button" @click="i = (i + 1) % n" class="absolute -right-4 top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white text-brand-700 shadow-md hover:bg-brand-50" aria-label="{{ __('Next') }}"><x-icon name="chevron-right" class="h-4 w-4" /></button>
                        </div>
                        <div class="mt-4 flex justify-center gap-1.5">
                            @foreach ($testimonials as $index => $review)
                                <button type="button" @click="i = {{ $index }}" :class="i === {{ $index }} ? 'w-6 bg-brand-700' : 'w-2 bg-slate-300'" class="h-2 rounded-full transition-all" aria-label="{{ __('Review') }} {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <a href="{{ route('testimonials.index') }}" class="mt-4 block text-center text-sm font-semibold text-brand-700 hover:text-brand-800">{{ __('Read all reviews') }} &rarr;</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ============ BLOG ============ --}}
    @if ($posts->isNotEmpty())
        <section class="bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <x-frontend.section-heading :eyebrow="__('Travel Blog')" :title="__('Tips, Guides & Travel News')" :link="route('blog.index')" :link-label="__('Read the blog')" />
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <x-frontend.blog-card :post="$post" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============ PARTNER AIRLINES ============ --}}
    <section class="bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <p class="text-center text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Our Airline Partners') }}</p>
            <div class="relative mt-6 overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
                <div class="flex w-max animate-[marquee_40s_linear_infinite] gap-4 hover:[animation-play-state:paused]">
                    @foreach (array_merge(...array_fill(0, 2, array_values(array_unique(array_merge(config('travel.airlines.domestic'), config('travel.airlines.international')))))) as $airline)
                        <span class="flex items-center gap-2 whitespace-nowrap rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-brand-900">
                            <x-icon name="plane" class="h-4 w-4 text-sky-500" /> {{ $airline }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============ INSTAGRAM GALLERY ============ --}}
    @if ($gallery->isNotEmpty())
        <section class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <x-frontend.section-heading :center="true" :eyebrow="__('@ramtours on Instagram')" :title="__('Moments From Our Travelers')" />
                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ($gallery as $item)
                        <a href="{{ $item->url }}" data-gallery="home-gallery" class="glightbox group relative block aspect-square overflow-hidden rounded-xl" data-title="{{ $item->title }}">
                            <img src="{{ $item->url }}" alt="{{ $item->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                            <span class="absolute inset-0 flex items-center justify-center bg-brand-950/0 text-white opacity-0 transition group-hover:bg-brand-950/40 group-hover:opacity-100">
                                <x-icon name="camera" class="h-7 w-7" />
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="mt-8 text-center">
                    <a href="{{ route('gallery.index') }}" class="inline-flex items-center gap-2 rounded-full border-2 border-brand-700 px-6 py-2.5 text-sm font-semibold text-brand-700 hover:bg-brand-700 hover:text-white transition">{{ __('View Gallery') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ MAP ============ --}}
    <section class="bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.6fr] gap-6 overflow-hidden rounded-3xl bg-white shadow-card ring-1 ring-slate-900/5">
                <div class="p-8">
                    <x-frontend.section-heading :eyebrow="__('Visit Our Office')" :title="__('We’d Love to Meet You')" />
                    <ul class="mt-6 space-y-4 text-sm text-slate-600">
                        <li class="flex gap-3"><x-icon name="map-pin" class="h-5 w-5 shrink-0 text-brand-700" />{{ site('address') }}</li>
                        <li class="flex gap-3"><x-icon name="phone" class="h-5 w-5 shrink-0 text-brand-700" /><span><a href="{{ site('phone_href') }}" class="hover:text-brand-700">{{ site('phone') }}</a> · <a href="{{ site('mobile_href') }}" class="hover:text-brand-700">{{ site('mobile') }}</a></span></li>
                        <li class="flex gap-3"><x-icon name="mail" class="h-5 w-5 shrink-0 text-brand-700" /><a href="mailto:{{ site('email') }}" class="hover:text-brand-700">{{ site('email') }}</a></li>
                        <li class="flex gap-3"><x-icon name="clock" class="h-5 w-5 shrink-0 text-brand-700" />{{ site('office_hours') }}</li>
                    </ul>
                    <a href="{{ route('contact') }}" class="mt-6 inline-flex items-center gap-2 rounded-full bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-800 transition">{{ __('Send an Inquiry') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
                </div>
                <iframe src="{{ site('map_embed') }}" class="h-72 lg:h-full min-h-[300px] w-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ __('Office location map') }}"></iframe>
            </div>
        </div>
    </section>

</x-site-layout>
