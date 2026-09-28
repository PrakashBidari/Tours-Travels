@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'account.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Travel Bookings', 'route' => 'account.travel-bookings.index', 'icon' => 'M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5'],
        ['label' => 'Hotel Stays', 'route' => 'account.trips.index', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15A1.5 1.5 0 0121 7.5v11.25A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75V7.5A1.5 1.5 0 014.5 6z'],
        ['label' => 'Vehicle Rental', 'route' => 'account.rental-requests.index', 'icon' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-7.635v7.635m0 0h-12m0 0h-3v-2.25a1.5 1.5 0 011.5-1.5h11.25a1.5 1.5 0 011.5 1.5v2.25'],
        ['label' => 'Wishlist', 'route' => 'account.wishlist.index', 'icon' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z'],
        ['label' => 'Cart', 'route' => 'account.cart.index', 'icon' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.943-4.706 2.44-7.184.075-.37-.221-.686-.596-.686H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z'],
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
        <a href="{{ route('account.dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <x-application-mark class="block h-8 w-auto shrink-0" />
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="font-semibold text-gray-800 dark:text-gray-100 text-lg whitespace-nowrap">My Account</span>
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
        <a href="{{ route('home') }}" :class="sidebarCollapsed ? 'lg:justify-center' : ''" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700/60 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            <span :class="sidebarCollapsed ? 'lg:hidden' : ''" class="whitespace-nowrap">Back to site</span>
        </a>
    </div>
</aside>
