@php
    $user = Auth::user();
    $icons = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15A1.5 1.5 0 0121 7.5v11.25A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75V7.5A1.5 1.5 0 014.5 6z',
        'globe' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418',
        'inbox' => 'M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z',
        'users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'tag' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z',
        'document' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
        'building' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21',
        'chart' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        'sliders' => 'M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75',
        'shield' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
        'cog' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z',
    ];
    $sb = fn (?string $service) => route('dashboard.service-bookings.index', $service ? ['service' => $service] : []);
    $onBookings = request()->routeIs('dashboard.service-bookings.*');

    // `can`: admin module (config/travel.php staff_roles); null = super admin only; 'any' = every admin user.
    $navGroups = [
        ['label' => 'Dashboard', 'icon' => $icons['home'], 'route' => 'dashboard', 'active' => request()->routeIs('dashboard'), 'can' => 'any'],
        ['label' => 'Travel Bookings', 'icon' => $icons['calendar'], 'can' => 'bookings', 'active' => $onBookings || request()->routeIs('dashboard.bookings.*'), 'children' => [
            ['label' => 'All Bookings', 'href' => $sb(null), 'active' => $onBookings && ! request('service')],
            ['label' => 'Tour Packages', 'href' => $sb('tour'), 'active' => request('service') === 'tour'],
            ['label' => 'Flight Tickets', 'href' => $sb('flight'), 'active' => request('service') === 'flight'],
            ['label' => 'Bus Tickets', 'href' => $sb('bus'), 'active' => request('service') === 'bus'],
            ['label' => 'Car Rentals', 'href' => $sb('car'), 'active' => request('service') === 'car'],
            ['label' => 'Visa Applications', 'href' => $sb('visa'), 'active' => request('service') === 'visa'],
            ...($user->isSuperAdmin() ? [['label' => 'Hotel Bookings', 'route' => 'dashboard.bookings.index']] : []),
        ]],
        ['label' => 'Catalogue', 'icon' => $icons['globe'], 'can' => 'catalog', 'active' => request()->routeIs('dashboard.tour-packages.*', 'dashboard.vehicles.*', 'dashboard.bus-routes.*', 'dashboard.visa-services.*'), 'children' => [
            ['label' => 'Tour Packages', 'route' => 'dashboard.tour-packages.index', 'active' => request()->routeIs('dashboard.tour-packages.*')],
            ['label' => 'Car Rental', 'route' => 'dashboard.vehicles.index', 'active' => request()->routeIs('dashboard.vehicles.*')],
            ['label' => 'Bus Routes & Seats', 'route' => 'dashboard.bus-routes.index', 'active' => request()->routeIs('dashboard.bus-routes.*')],
            ['label' => 'Visa Services', 'route' => 'dashboard.visa-services.index', 'active' => request()->routeIs('dashboard.visa-services.*')],
        ]],
        ['label' => 'Inquiries', 'icon' => $icons['inbox'], 'route' => 'dashboard.inquiries.index', 'active' => request()->routeIs('dashboard.inquiries.*', 'dashboard.contact-messages.*'), 'can' => 'inquiries'],
        ['label' => 'Customers', 'icon' => $icons['users'], 'route' => 'dashboard.customers.index', 'active' => request()->routeIs('dashboard.customers.*'), 'can' => 'customers'],
        ['label' => 'Marketing', 'icon' => $icons['tag'], 'can' => 'marketing', 'active' => request()->routeIs('dashboard.offers.*', 'dashboard.testimonials.*', 'dashboard.subscribers.*'), 'children' => [
            ['label' => 'Offers & Coupons', 'route' => 'dashboard.offers.index', 'active' => request()->routeIs('dashboard.offers.*')],
            ['label' => 'Testimonials', 'route' => 'dashboard.testimonials.index', 'active' => request()->routeIs('dashboard.testimonials.*')],
            ['label' => 'Newsletter', 'route' => 'dashboard.subscribers.index', 'active' => request()->routeIs('dashboard.subscribers.*')],
        ]],
        ['label' => 'Content', 'icon' => $icons['document'], 'can' => 'content', 'active' => request()->routeIs('dashboard.blog-posts.*', 'dashboard.faqs.*', 'dashboard.gallery.*'), 'children' => [
            ['label' => 'Blog Posts', 'route' => 'dashboard.blog-posts.index', 'active' => request()->routeIs('dashboard.blog-posts.*')],
            ['label' => 'Gallery', 'route' => 'dashboard.gallery.index', 'active' => request()->routeIs('dashboard.gallery.*')],
            ['label' => 'FAQ', 'route' => 'dashboard.faqs.index', 'active' => request()->routeIs('dashboard.faqs.*')],
        ]],
        ['label' => 'Website', 'icon' => $icons['sliders'], 'can' => null, 'active' => request()->routeIs('dashboard.page-settings.*', 'dashboard.header-settings.*', 'dashboard.home-order.*'), 'children' => [
            ['label' => 'Site & Homepage Settings', 'route' => 'dashboard.page-settings.index'],
            ['label' => 'Header & Menu', 'route' => 'dashboard.header-settings.index'],
            ['label' => 'Homepage Hotel Order', 'route' => 'dashboard.home-order.index'],
        ]],
        ['label' => 'Hotels & Partners', 'icon' => $icons['building'], 'can' => null, 'active' => request()->routeIs('dashboard.packages.*', 'dashboard.destinations.*', 'dashboard.vendors.*', 'dashboard.rental-partners.*'), 'children' => [
            ['label' => 'Hotels / Properties', 'route' => 'dashboard.packages.index'],
            ['label' => 'Destinations', 'route' => 'dashboard.destinations.index'],
            ['label' => 'Vendors', 'route' => 'dashboard.vendors.index'],
            ['label' => 'Rental Partners', 'route' => 'dashboard.rental-partners.index'],
        ]],
        ['label' => 'Revenue', 'icon' => $icons['chart'], 'route' => 'dashboard.revenue.index', 'active' => request()->routeIs('dashboard.revenue.*'), 'can' => null],
        ['label' => 'Users & Staff', 'icon' => $icons['shield'], 'can' => null, 'active' => request()->routeIs('dashboard.users.*', 'dashboard.staff.*'), 'children' => [
            ['label' => 'Staff & Roles', 'route' => 'dashboard.staff.index', 'active' => request()->routeIs('dashboard.staff.*')],
            ['label' => 'Traveler Accounts', 'route' => 'dashboard.users.index', 'active' => request()->routeIs('dashboard.users.*')],
        ]],
        ['label' => 'Settings', 'icon' => $icons['cog'], 'can' => 'any', 'active' => request()->routeIs('profile.show') || request()->routeIs('api-tokens.index'), 'children' => [
            ['label' => 'Profile', 'route' => 'profile.show'],
            ['label' => 'Security & 2FA', 'route' => 'profile.show'],
            ...\Laravel\Jetstream\Jetstream::hasApiFeatures() ? [['label' => 'API Tokens', 'route' => 'api-tokens.index']] : [],
        ]],
    ];

    $navGroups = array_values(array_filter($navGroups, fn ($group) => match ($group['can']) {
        'any' => true,
        null => $user->isSuperAdmin(),
        default => $user->canAccessAdmin($group['can']),
    }));
@endphp

<aside x-cloak
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-16' : 'lg:w-64',
    ]"
    class="fixed inset-y-0 left-0 z-40 flex w-64 transform flex-col border-r border-gray-200 bg-white transition-all duration-200 ease-in-out dark:border-gray-700 dark:bg-gray-800 lg:translate-x-0">
    <!-- Collapse/expand toggle (large screens only) -->
    <button type="button"
        @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
        class="absolute -right-3 top-6 z-10 hidden h-6 w-6 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-400 shadow-sm hover:text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:hover:text-gray-200 lg:flex">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform"
            :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <!-- Logo -->
    <div
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-gray-200 px-5 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <x-application-mark class="block h-8 w-auto shrink-0" />
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''"
                class="whitespace-nowrap text-[15px] font-semibold text-gray-800 dark:text-gray-100">{{ config('app.name', 'Booking') }}</span>
        </a>

        <!-- Mobile close button -->
        <button type="button" @click="sidebarOpen = false"
            class="rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700/60 dark:hover:text-gray-200 lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($navGroups as $group)
            @if (isset($group['children']))
                <div class="group relative" x-data="{ open: {{ $group['active'] ? 'true' : 'false' }} }">
                    <button type="button" @click="sidebarCollapsed ? null : (open = !open)"
                        class="{{ $group['active']
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700/60' }} flex w-full items-center justify-between gap-3 rounded-lg px-3 py-2 text-sm font-medium transition"
                        :class="sidebarCollapsed ? 'lg:justify-center' : ''">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $group['icon'] }}" />
                            </svg>
                            <span :class="sidebarCollapsed ? 'lg:hidden' : ''"
                                class="whitespace-nowrap">{{ $group['label'] }}</span>
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 transition-transform"
                            :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div x-show="open && !sidebarCollapsed" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" class="ml-8 mt-1 space-y-1">

                        @foreach ($group['children'] as $child)
                            <a href="{{ isset($child['route']) ? route($child['route']) : $child['href'] }}"
                                class="{{ ($child['active'] ?? (isset($child['route']) && request()->routeIs($child['route'])))
                                    ? 'text-indigo-700 dark:text-indigo-400 font-medium'
                                    : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-100' }} block rounded-md px-3 py-1.5 text-sm transition">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Flyout (collapsed large screens) -->
                    <div x-show="sidebarCollapsed" x-cloak
                        class="invisible absolute left-full top-0 z-50 ml-1 hidden min-w-[180px] rounded-lg bg-white py-2 opacity-0 shadow-lg ring-1 ring-gray-900/5 transition group-hover:visible group-hover:opacity-100 dark:bg-gray-800 dark:ring-white/10 lg:block">
                        <div class="px-3 pb-1.5 text-xs font-semibold text-gray-400">{{ $group['label'] }}</div>
                        @foreach ($group['children'] as $child)
                            <a href="{{ isset($child['route']) ? route($child['route']) : $child['href'] }}"
                                class="block px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700/60">
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="group relative">
                    <a href="{{ isset($group['route']) ? route($group['route']) : $group['href'] }}"
                        class="{{ $group['active']
                            ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400'
                            : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700/60' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition"
                        :class="sidebarCollapsed ? 'lg:justify-center' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $group['icon'] }}" />
                        </svg>
                        <span :class="sidebarCollapsed ? 'lg:hidden' : ''"
                            class="whitespace-nowrap">{{ $group['label'] }}</span>
                    </a>

                    <!-- Flyout (collapsed large screens) -->
                    <a href="{{ isset($group['route']) ? route($group['route']) : $group['href'] }}"
                        x-show="sidebarCollapsed" x-cloak
                        class="invisible absolute left-full top-0 z-50 ml-1 hidden whitespace-nowrap rounded-lg bg-white px-3 py-2 text-sm font-medium text-gray-700 opacity-0 shadow-lg ring-1 ring-gray-900/5 transition hover:bg-gray-50 group-hover:visible group-hover:opacity-100 dark:bg-gray-800 dark:text-gray-200 dark:ring-white/10 dark:hover:bg-gray-700/60 lg:block">
                        {{ $group['label'] }}
                    </a>
                </div>
            @endif
        @endforeach
    </nav>

    <!-- Theme toggle -->
    <div class="shrink-0 border-t border-gray-200 px-3 py-4 dark:border-gray-700">
        <button type="button" x-data="{ dark: document.documentElement.classList.contains('dark') }"
            @click="
                dark = !dark;
                document.documentElement.classList.toggle('dark', dark);
                localStorage.setItem('theme', dark ? 'dark' : 'light');
            "
            class="flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700/60"
            :class="sidebarCollapsed ? 'lg:justify-center' : 'justify-between'">
            <span class="flex items-center gap-3">
                <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="whitespace-nowrap"
                    x-text="dark ? 'Dark theme' : 'Light theme'"></span>
            </span>
            <span class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition"
                :class="[sidebarCollapsed ? 'lg:hidden' : '', dark ? 'bg-indigo-600' : 'bg-gray-300']">
                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition"
                    :class="dark ? 'translate-x-5' : 'translate-x-1'"></span>
            </span>
        </button>
    </div>
</aside>
