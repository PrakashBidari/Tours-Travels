@php
    $siteName = site('name');
    $pageTitle = $title ? $title : ($pageSettings->meta_title ?: $siteName);
    $metaDescription = $description ?: ($pageSettings->meta_description ?: config('travel.company.name').' — Nepal & international tours, flights, bus tickets, car rental, hotels and visa services from Kathmandu.');
    $metaImage = $image ?: $pageSettings->hero_image_url;
    $canonicalUrl = $canonical ?: url()->current();
    $currentPath = '/'.ltrim(request()->path(), '/');

    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'TravelAgency',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => $headerSettings->logo_url ?: asset('favicon.ico'),
        'image' => $pageSettings->hero_image_url,
        'telephone' => site('phone'),
        'email' => site('email'),
        'priceRange' => 'Rs. 1,000 – Rs. 5,00,000',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Pabitra Nagar, Gangabu',
            'addressLocality' => 'Kathmandu',
            'addressCountry' => 'NP',
        ],
        'sameAs' => $footerSocialLinks->pluck('url')->values()->all(),
    ];

    if ($breadcrumbs) {
        $schema[] = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect([[__('Home'), url('/')], ...$breadcrumbs])->values()->map(fn ($crumb, $i) => array_filter([
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb[0],
                'item' => $crumb[1] ?? null,
            ]))->all(),
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ Str::limit(strip_tags($metaDescription), 160) }}">
        <meta name="keywords" content="Nepal tour packages, travel agency Kathmandu, flight tickets Nepal, bus tickets Kathmandu Pokhara, car rental Kathmandu, visa services Nepal, Everest trek, {{ $siteName }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <link rel="alternate" hreflang="en" href="{{ $canonicalUrl }}">
        <link rel="alternate" hreflang="ne" href="{{ $canonicalUrl }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ Str::limit(strip_tags($metaDescription), 200) }}">
        <meta property="og:image" content="{{ $metaImage }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($metaDescription), 200) }}">
        <meta name="twitter:image" content="{{ $metaImage }}">
        <meta name="theme-color" content="#0047AB">

        <script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @foreach ($schema as $block)
            <script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://images.unsplash.com">

        @if ($pageSettings->google_analytics_id)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $pageSettings->google_analytics_id }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', @json($pageSettings->google_analytics_id));
            </script>
        @endif

        @if ($pageSettings->facebook_pixel_id)
            <script>
                !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', @json($pageSettings->facebook_pixel_id));
                fbq('track', 'PageView');
            </script>
        @endif

        <x-flash-alerts />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-800">
        <div x-data="{ mobileNavOpen: false, navDrawer: false, navVisible: {{ $navItems->count() }}, scrolled: false }" @scroll.window="scrolled = window.scrollY > 40" x-effect="document.documentElement.classList.toggle('overflow-hidden', navDrawer)" @keydown.escape.window="navDrawer = false" class="min-h-screen flex flex-col">

            <!-- Top bar -->
            <div class="hidden md:block bg-brand-950 text-white/90 text-[13px]">
                <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 h-10 flex items-center justify-between gap-6">
                    <div class="flex items-center gap-6 min-w-0">
                        <span class="flex items-center gap-1.5 truncate">
                            <svg class="h-4 w-4 text-gold-400 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg>
                            {{ site('address') }}
                        </span>
                        <a href="{{ site('phone_href') }}" class="hidden lg:flex items-center gap-1.5 hover:text-gold-400 transition">
                            <svg class="h-4 w-4 text-gold-400" viewBox="0 0 24 24" fill="currentColor"><path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
                            {{ site('phone') }}
                        </a>
                        <a href="{{ site('mobile_href') }}" class="hidden lg:flex items-center gap-1.5 hover:text-gold-400 transition">
                            <svg class="h-4 w-4 text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18h2"/></svg>
                            {{ site('mobile') }}
                        </a>
                        <a href="mailto:{{ site('email') }}" class="hidden xl:flex items-center gap-1.5 hover:text-gold-400 transition">
                            <svg class="h-4 w-4 text-gold-400" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4.2-8 5-8-5V6l8 5 8-5z"/></svg>
                            {{ site('email') }}
                        </a>
                    </div>

                    <div class="flex items-center gap-4 shrink-0">
                        <div class="flex items-center gap-1.5 font-medium">
                            <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-gold-400' : 'hover:text-gold-400' }}">EN</a>
                            <span class="text-white/30">|</span>
                            <a href="{{ route('locale.switch', 'ne') }}" class="{{ app()->getLocale() === 'ne' ? 'text-gold-400' : 'hover:text-gold-400' }}">नेपाली</a>
                        </div>

                        <form method="POST" action="{{ route('currency.switch') }}" class="flex items-center">
                            @csrf
                            <label for="currency-switch" class="sr-only">{{ __('Currency') }}</label>
                            <select id="currency-switch" name="currency" onchange="this.form.submit()" class="bg-transparent border-0 py-0 pl-0 pr-6 text-[13px] font-medium text-white focus:ring-0 cursor-pointer [&>option]:text-slate-800">
                                @foreach (config('travel.currencies') as $code => $currency)
                                    <option value="{{ $code }}" @selected(session('currency', 'NPR') === $code)>{{ $code }}</option>
                                @endforeach
                            </select>
                        </form>

                        <div class="flex items-center gap-3 pl-3 border-l border-white/15">
                            @foreach ($footerSocialLinks as $link)
                                <a href="{{ $link->url }}" target="_blank" rel="noopener" title="{{ $link->label }}" class="hover:text-gold-400 transition">
                                    <x-social-icon :platform="$link->platform" class="h-4 w-4" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header -->
            <header class="sticky top-0 z-40 bg-white/95 backdrop-blur transition-shadow" :class="scrolled ? 'shadow-lg shadow-brand-950/5' : 'shadow-sm'"
                x-data="{
                    check() {
                        // The menu icon lives outside the menu area; BTN is its width + gap, reserved only when items overflow.
                        const BTN = 48;
                        const items = [...this.$refs.nav.children];
                        items.forEach(el => el.style.display = '');
                        const avail = this.$refs.slot.clientWidth + (this.navVisible < items.length ? BTN : 0);
                        const widths = items.map(el => el.offsetWidth);
                        let count = items.length;
                        if (widths.reduce((a, b) => a + b, 0) > avail) {
                            const room = avail - BTN;
                            let used = 0;
                            count = 0;
                            for (const w of widths) { if (used + w > room) break; used += w; count++; }
                        }
                        items.forEach((el, i) => el.style.display = i < count ? '' : 'none');
                        this.navVisible = count;
                        if (count === items.length) this.navDrawer = false;
                    }
                }"
                x-init="$nextTick(() => check()); new ResizeObserver(() => check()).observe($refs.slot); document.fonts?.ready.then(() => check())">
                <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="h-[72px] lg:h-20 flex items-center justify-between gap-4">
                        <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $siteName }}">
                            @if ($headerSettings->logo_url)
                                <img src="{{ $headerSettings->logo_url }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain">
                            @else
                                <x-ram-logo />
                            @endif
                        </a>

                        <!-- Desktop menu: inline while it fits, otherwise it moves into the right-hand drawer -->
                        <div x-ref="slot" class="relative hidden lg:flex flex-1 min-w-0 items-center justify-center">
                        <nav x-ref="nav" class="flex shrink-0 items-center text-[13.5px] 2xl:text-sm font-medium text-slate-700" aria-label="Main">
                            @foreach ($navItems as $item)
                                @php
                                    $itemPath = '/'.ltrim(parse_url($item->url, PHP_URL_PATH) ?? '', '/');
                                    $isActive = $itemPath === '/' ? $currentPath === '/' : str_starts_with($currentPath, $itemPath);
                                @endphp
                                @if ($item->children->isNotEmpty())
                                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                        <a href="{{ $item->url }}" target="{{ $item->target }}" @if ($item->target === '_blank') rel="noopener" @endif
                                            class="relative inline-flex items-center gap-0.5 whitespace-nowrap px-1.5 2xl:px-2 py-2 rounded-md transition {{ $isActive ? 'text-brand-700' : 'hover:text-brand-700' }}">
                                            {{ __($item->label) }}
                                            <svg class="h-3.5 w-3.5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                            @if ($isActive)<span class="absolute left-1.5 right-1.5 -bottom-0.5 h-0.5 rounded-full bg-brand-700"></span>@endif
                                        </a>
                                        <div x-show="open" x-cloak x-transition.origin.top class="absolute left-0 top-full z-50 pt-2">
                                            <div class="min-w-[220px] rounded-xl bg-white p-2 shadow-xl ring-1 ring-slate-900/5">
                                                @foreach ($item->children as $child)
                                                    <a href="{{ $child->url }}" target="{{ $child->target }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-brand-50 hover:text-brand-700 transition">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>{{ __($child->label) }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $item->url }}" target="{{ $item->target }}" @if ($item->target === '_blank') rel="noopener" @endif
                                        class="relative whitespace-nowrap px-1.5 2xl:px-2 py-2 rounded-md transition {{ $isActive ? 'text-brand-700' : 'hover:text-brand-700' }}">
                                        {{ __($item->label) }}
                                        @if ($isActive)<span class="absolute left-1.5 right-1.5 -bottom-0.5 h-0.5 rounded-full bg-brand-700"></span>@endif
                                    </a>
                                @endif
                            @endforeach
                        </nav>

                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <button type="button" x-show="navVisible < {{ $navItems->count() }}" x-cloak @click="navDrawer = true"
                                class="hidden lg:inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-brand-700 hover:bg-brand-50 transition" aria-label="{{ __('More menu items') }}" :aria-expanded="navDrawer">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" /></svg>
                            </button>

                            <a href="{{ route('tours.index') }}" data-wishlist-link title="{{ __('My wishlist') }}" class="relative hidden h-10 w-10 items-center justify-center rounded-full text-rose-500 hover:bg-rose-50 transition">
                                <x-icon name="heart" :solid="true" class="h-5 w-5" />
                                <span data-count class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand-700 px-1 text-[10px] font-bold text-white"></span>
                            </a>
                            @auth
                                <a href="{{ Auth::user()->dashboardUrl() }}" title="{{ __('My Account') }}" class="hidden sm:inline-flex h-10 w-10 items-center justify-center rounded-full ring-2 ring-brand-100 hover:ring-brand-300 transition overflow-hidden">
                                    <img class="h-full w-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </a>
                            @else
                                <a href="{{ route('login') }}" title="{{ __('Sign in') }}" class="hidden sm:inline-flex items-center gap-1.5 whitespace-nowrap px-2 py-2 text-sm font-medium text-slate-600 hover:text-brand-700 transition">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.1a7.5 7.5 0 0115 0A17.9 17.9 0 0112 21.75c-2.68 0-5.22-.58-7.5-1.65z" /></svg>
                                    <span>{{ __('Sign in') }}</span>
                                </a>
                            @endauth

                            <a href="{{ route('tours.index') }}" class="hidden sm:inline-flex items-center gap-2 whitespace-nowrap rounded-full bg-gold-500 px-4 2xl:px-5 py-2.5 text-sm font-semibold text-brand-950 shadow-md shadow-gold-500/30 hover:bg-gold-400 hover:-translate-y-0.5 transition">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5z"/></svg>
                                {{ __('Book Now') }}
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                            </a>



                            <button type="button" @click="mobileNavOpen = !mobileNavOpen" class="lg:hidden p-2 rounded-lg text-brand-700 hover:bg-brand-50" aria-label="Toggle menu">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path x-show="!mobileNavOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                                    <path x-show="mobileNavOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile nav -->
                <div x-show="mobileNavOpen" x-cloak x-transition class="lg:hidden border-t border-slate-100 bg-white max-h-[calc(100vh-72px)] overflow-y-auto">
                    <div class="px-4 py-3 space-y-1">
                        @foreach ($navItems as $item)
                            @if ($item->children->isNotEmpty())
                                <div x-data="{ open: false }">
                                    <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg font-medium text-slate-700 hover:bg-brand-50">
                                        <span>{{ __($item->label) }}</span>
                                        <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <div x-show="open" x-cloak x-transition class="ml-3 border-l-2 border-brand-100 pl-2">
                                        <a href="{{ $item->url }}" class="block px-3 py-2 rounded-lg text-sm text-slate-600 hover:bg-brand-50">{{ __('View all') }}</a>
                                        @foreach ($item->children as $child)
                                            <a href="{{ $child->url }}" class="block px-3 py-2 rounded-lg text-sm text-slate-600 hover:bg-brand-50">{{ __($child->label) }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $item->url }}" class="block px-3 py-2.5 rounded-lg font-medium text-slate-700 hover:bg-brand-50">{{ __($item->label) }}</a>
                            @endif
                        @endforeach

                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100">
                            @auth
                                <a href="{{ Auth::user()->dashboardUrl() }}" class="text-center rounded-full border border-brand-200 px-4 py-2.5 text-sm font-semibold text-brand-700">{{ __('My Account') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="text-center rounded-full border border-brand-200 px-4 py-2.5 text-sm font-semibold text-brand-700">{{ __('Sign in') }}</a>
                            @endauth
                            <a href="{{ route('tours.index') }}" class="text-center rounded-full bg-gold-500 px-4 py-2.5 text-sm font-semibold text-brand-950">{{ __('Book Now') }}</a>
                        </div>

                        <div class="flex items-center justify-between pt-3 text-sm">
                            <div class="flex items-center gap-2 font-medium">
                                <a href="{{ route('locale.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'text-brand-700' : 'text-slate-500' }}">EN</a>
                                <span class="text-slate-300">|</span>
                                <a href="{{ route('locale.switch', 'ne') }}" class="{{ app()->getLocale() === 'ne' ? 'text-brand-700' : 'text-slate-500' }}">नेपाली</a>
                            </div>
                            <a href="{{ site('phone_href') }}" class="font-semibold text-brand-700">{{ site('phone') }}</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Right-hand drawer: holds only the header menu items that did not fit (large screens). Sign in / Book Now stay in the header. -->
            <div x-show="navDrawer" x-cloak class="fixed inset-0 z-[60] hidden lg:block" role="dialog" aria-modal="true" aria-label="{{ __('Menu') }}">
                <div x-show="navDrawer" x-transition.opacity.duration.300ms @click="navDrawer = false" class="absolute inset-0 bg-brand-950/50 backdrop-blur-sm"></div>
                <aside x-show="navDrawer"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="absolute inset-y-0 right-0 flex w-[360px] max-w-[90vw] flex-col bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <x-ram-logo :compact="true" />
                        <button type="button" @click="navDrawer = false" class="rounded-full p-2 text-slate-500 hover:bg-slate-100 hover:text-brand-700" aria-label="{{ __('Close menu') }}">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1" aria-label="{{ __('Main') }}">
                        @foreach ($navItems as $item)
                            @php
                                $itemPath = '/'.ltrim(parse_url($item->url, PHP_URL_PATH) ?? '', '/');
                                $isActive = $itemPath === '/' ? $currentPath === '/' : str_starts_with($currentPath, $itemPath);
                            @endphp
                            @if ($item->children->isNotEmpty())
                                <div x-show="{{ $loop->index }} >= navVisible" x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
                                    <button type="button" @click="open = !open" :aria-expanded="open" class="flex w-full items-center justify-between rounded-lg px-3 py-2.5 font-medium transition {{ $isActive ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-brand-50' }}">
                                        <span>{{ __($item->label) }}</span>
                                        <svg class="h-4 w-4 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <div x-show="open" x-collapse class="ml-3 border-l-2 border-brand-100 pl-2">
                                        <a href="{{ $item->url }}" class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-brand-50 hover:text-brand-700">{{ __('View all') }}</a>
                                        @foreach ($item->children as $child)
                                            <a href="{{ $child->url }}" target="{{ $child->target }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-brand-50 hover:text-brand-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>{{ __($child->label) }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a x-show="{{ $loop->index }} >= navVisible" href="{{ $item->url }}" target="{{ $item->target }}" @if ($item->target === '_blank') rel="noopener" @endif
                                    class="block rounded-lg px-3 py-2.5 font-medium transition {{ $isActive ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-brand-50' }}">{{ __($item->label) }}</a>
                            @endif
                        @endforeach
                    </nav>

                    <div class="border-t border-slate-100 bg-slate-50 px-6 py-5 text-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ __('Need help?') }}</p>
                        <a href="{{ site('phone_href') }}" class="mt-2 flex items-center gap-2 font-semibold text-brand-700"><x-icon name="phone" class="h-4 w-4" /> {{ site('phone') }}</a>
                        <a href="https://wa.me/{{ site('whatsapp') }}" target="_blank" rel="noopener" class="mt-1.5 flex items-center gap-2 font-semibold text-emerald-600"><x-social-icon platform="whatsapp" class="h-4 w-4" /> {{ site('mobile') }}</a>
                        <a href="mailto:{{ site('email') }}" class="mt-1.5 flex items-center gap-2 text-slate-600"><x-icon name="mail" class="h-4 w-4" /> {{ site('email') }}</a>
                    </div>
                </aside>
            </div>

            <!-- Page Content -->
            <main class="flex-1">
                {{ $slot }}
            </main>

            <!-- CTA band -->
            <section class="relative overflow-hidden">
                <img src="{{ config('travel.images.mountains') }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-r from-brand-950/95 via-brand-800/85 to-brand-700/70"></div>
                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-5 text-white">
                        <svg class="hidden sm:block h-14 w-14 text-white shrink-0 -rotate-12" viewBox="0 0 24 24" fill="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5z"/></svg>
                        <div class="text-center md:text-left">
                            <h2 class="text-2xl sm:text-3xl font-bold">{{ __('Ready to Start Your Next Adventure?') }}</h2>
                            <p class="mt-1 text-brand-100">{{ __("Get in touch with us today and let's plan your perfect trip!") }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <a href="https://wa.me/{{ site('whatsapp') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full border border-white/40 px-6 py-3 text-sm font-semibold text-white hover:bg-white/10 transition">
                            <x-social-icon platform="whatsapp" class="h-4 w-4" /> WhatsApp
                        </a>
                        <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-full bg-gold-500 px-7 py-3 text-sm font-semibold text-brand-950 shadow-lg shadow-gold-500/30 hover:bg-gold-400 transition">
                            {{ __('Contact Us') }}
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="bg-brand-950 text-brand-100/80">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-12 gap-8 lg:gap-10">
                        <div class="col-span-2 md:col-span-3 lg:col-span-4">
                            @if ($footerIconUrl)
                                <img src="{{ $footerIconUrl }}" alt="{{ $siteName }}" class="h-12 w-auto">
                            @else
                                <x-ram-logo :light="true" />
                            @endif
                            <p class="mt-5 text-sm leading-relaxed">{{ $footerTagline ?: config('travel.company.name') }}</p>

                            <form method="POST" action="{{ route('newsletter.subscribe') }}" class="mt-6">
                                @csrf
                                <label for="newsletter-email" class="text-sm font-semibold text-white">{{ __('Subscribe to our newsletter') }}</label>
                                <div class="mt-2 flex rounded-full bg-white/10 p-1 ring-1 ring-white/10 focus-within:ring-gold-400">
                                    <input id="newsletter-email" type="email" name="email" required placeholder="{{ __('Your email address') }}" class="min-w-0 flex-1 border-0 bg-transparent px-4 text-sm text-white placeholder:text-brand-200/60 focus:ring-0">
                                    <button class="rounded-full bg-gold-500 px-4 py-2 text-sm font-semibold text-brand-950 hover:bg-gold-400 transition">{{ __('Subscribe') }}</button>
                                </div>
                            </form>
                        </div>

                        @foreach ($footerColumns as $column)
                            <div class="lg:col-span-2">
                                <h4 class="text-sm font-semibold text-white">{{ __($column->heading) }}</h4>
                                <span class="mt-2 block h-0.5 w-8 rounded bg-gold-500"></span>
                                <ul class="mt-4 space-y-2.5 text-sm">
                                    @foreach ($column->links as $link)
                                        <li><a href="{{ $link->url }}" class="hover:text-gold-400 transition">{{ __($link->label) }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach

                        <div class="col-span-2 md:col-span-3 lg:col-span-2">
                            <h4 class="text-sm font-semibold text-white">{{ __('Contact Us') }}</h4>
                            <span class="mt-2 block h-0.5 w-8 rounded bg-gold-500"></span>
                            <ul class="mt-4 space-y-3 text-sm">
                                <li>{{ site('address') }}</li>
                                <li><a href="{{ site('phone_href') }}" class="hover:text-gold-400">{{ site('phone') }}</a><br><a href="{{ site('mobile_href') }}" class="hover:text-gold-400">{{ site('mobile') }}</a></li>
                                <li><a href="mailto:{{ site('email') }}" class="hover:text-gold-400 break-all">{{ site('email') }}</a></li>
                                <li class="text-xs text-brand-200/70">{{ site('office_hours') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-white/10 pt-6">
                        <span class="text-xs font-semibold uppercase tracking-wider text-white/60">{{ __('We accept') }}</span>
                        @foreach (config('travel.payment_methods') as $key => $method)
                            @continue($key === 'office')
                            <span class="rounded-md bg-white px-2.5 py-1 text-[11px] font-bold" style="color: {{ $method['color'] }}">{{ $method['label'] }}</span>
                        @endforeach
                        <span class="ml-auto hidden lg:block text-xs text-brand-200/60">{{ implode(' · ', config('travel.company.affiliations')) }}</span>
                    </div>
                </div>

                <div class="bg-black/25">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-3 text-xs">
                        <div class="text-brand-200/80 text-center md:text-left [&_a]:underline">{!! $footerCopyright !!}</div>
                        <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                            @if (Route::has('policy.show'))<a href="{{ route('policy.show') }}" class="hover:text-gold-400">{{ __('Privacy Policy') }}</a>@endif
                            @if (Route::has('terms.show'))<a href="{{ route('terms.show') }}" class="hover:text-gold-400">{{ __('Terms & Conditions') }}</a>@endif
                            <a href="{{ route('pages.refund') }}" class="hover:text-gold-400">{{ __('Refund Policy') }}</a>
                            <a href="{{ route('pages.cookies') }}" class="hover:text-gold-400">{{ __('Cookie Policy') }}</a>
                            <a href="{{ url('/sitemap.xml') }}" class="hover:text-gold-400">{{ __('Sitemap') }}</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Floating actions: travel assistant, WhatsApp, back-to-top -->
        <div x-data="{ helpOpen: false, showTop: false }" @scroll.window="showTop = window.scrollY > 600" class="fixed bottom-5 right-5 z-50 flex flex-col items-end gap-3">
            <div x-show="helpOpen" x-cloak x-transition.origin.bottom.right @click.outside="helpOpen = false" class="w-[300px] max-w-[calc(100vw-2.5rem)] overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/10">
                <div class="bg-gradient-to-r from-brand-700 to-sky-500 p-4 text-white">
                    <p class="font-semibold">{{ __('Travel Assistant') }}</p>
                    <p class="text-xs text-white/80">{{ __('Hi! How can we help you plan your trip?') }}</p>
                </div>
                <div class="p-3 grid grid-cols-2 gap-2 text-sm">
                    @foreach ([[__('Tour Packages'), route('tours.index')], [__('Flight Tickets'), route('flights.index')], [__('Bus Tickets'), route('bus.index')], [__('Car Rental'), route('cars.index')], [__('Visa Services'), route('visa.index')], [__('Track Booking'), route('booking.track')]] as [$label, $url])
                        <a href="{{ $url }}" class="rounded-lg bg-brand-50 px-3 py-2 text-center font-medium text-brand-700 hover:bg-brand-100 transition">{{ $label }}</a>
                    @endforeach
                </div>
                <div class="border-t border-slate-100 p-3 space-y-2">
                    <a href="https://wa.me/{{ site('whatsapp') }}?text={{ urlencode(__('Hello Ram Tours, I would like to plan a trip.')) }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 rounded-full bg-[#25D366] px-4 py-2.5 text-sm font-semibold text-white hover:brightness-95">
                        <x-social-icon platform="whatsapp" class="h-4 w-4" /> {{ __('Chat on WhatsApp') }}
                    </a>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ site('messenger') }}" target="_blank" rel="noopener" class="rounded-full border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-700 hover:border-brand-300">Messenger</a>
                        <a href="{{ site('phone_href') }}" class="rounded-full border border-slate-200 px-3 py-2 text-center text-xs font-semibold text-slate-700 hover:border-brand-300">{{ __('Call Us') }}</a>
                    </div>
                </div>
            </div>

            <button type="button" x-show="showTop" x-cloak x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })" class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-brand-700 shadow-lg ring-1 ring-slate-900/5 hover:bg-brand-50" aria-label="Back to top">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
            </button>

            <button type="button" @click="helpOpen = !helpOpen" class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-700 text-white shadow-lg shadow-brand-700/40 hover:bg-brand-800" aria-label="{{ __('Travel Assistant') }}">
                <svg x-show="!helpOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                <svg x-show="helpOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            <a href="https://wa.me/{{ site('whatsapp') }}" target="_blank" rel="noopener" class="flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg shadow-green-600/40 hover:scale-105 transition" aria-label="WhatsApp">
                <x-social-icon platform="whatsapp" class="h-7 w-7" />
            </a>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
