@php
    $vendorApproved = Auth::user()->isApprovedVendor();

    $navItems = [
        ['label' => 'Dashboard', 'route' => 'vendor.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ...($vendorApproved ? [
            ['label' => 'Destinations', 'route' => 'vendor.destinations.index', 'icon' => 'M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z'],
            ['label' => 'My Packages', 'route' => 'vendor.packages.index', 'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21'],
            ['label' => 'Bookings', 'route' => 'vendor.bookings.index', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15A1.5 1.5 0 0121 7.5v11.25A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75V7.5A1.5 1.5 0 014.5 6z'],
        ] : []),
        ['label' => 'Profile & Password', 'route' => 'profile.show', 'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z'],
    ];
@endphp

<aside x-cloak :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-16' : 'lg:w-64']"
    class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-all duration-200 ease-in-out lg:translate-x-0">

    <button type="button" @click="sidebarCollapsed = !sidebarCollapsed; localStorage.setItem('sidebarCollapsed', sidebarCollapsed)"
        class="hidden lg:flex absolute -right-3 top-6 h-6 w-6 items-center justify-center rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 z-10">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
    </button>

    <div class="h-16 flex items-center justify-between gap-2 px-5 border-b border-gray-200 dark:border-gray-700 shrink-0">
        <a href="{{ route('vendor.dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <x-application-mark class="block h-8 w-auto shrink-0" />
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="font-semibold text-gray-800 dark:text-gray-100 text-lg whitespace-nowrap">Vendor Panel</span>
        </a>
        <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
        @foreach ($navItems as $item)
            @php $active = request()->routeIs($item['route'].'*'); @endphp
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
                    {{ $active ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700/60' }}"
                :class="sidebarCollapsed ? 'lg:justify-center' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="whitespace-nowrap">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-3 py-4 border-t border-gray-200 dark:border-gray-700 shrink-0">
        <button type="button" x-data="{ dark: document.documentElement.classList.contains('dark') }"
            @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('theme', dark ? 'dark' : 'light');"
            class="w-full flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700/60 transition"
            :class="sidebarCollapsed ? 'lg:justify-center' : 'justify-between'">
            <span class="flex items-center gap-3">
                <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                <svg x-show="dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="whitespace-nowrap" x-text="dark ? 'Dark theme' : 'Light theme'"></span>
            </span>
        </button>
    </div>
</aside>
