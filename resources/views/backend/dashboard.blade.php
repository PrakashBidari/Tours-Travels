<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ Auth::user()->isSuperAdmin() ? 'Ram Tours Admin Dashboard' : (Auth::user()->staff_role_label.' Dashboard') }}
        </h2>
    </x-slot>

    @php
        $user = Auth::user();
        $bookingsUrl = fn (array $filters = []) => route('dashboard.service-bookings.index', $filters);
        $icons = [
            'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15A1.5 1.5 0 0121 7.5v11.25A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75V7.5A1.5 1.5 0 014.5 6z',
            'banknotes' => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z',
            'clock' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
            'check' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'x' => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'users' => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
            'map' => 'M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z',
            'eye' => 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'inbox' => 'M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z',
            'building' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21',
            'truck' => 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12',
            'tag' => 'M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z',
            'pencil' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10',
            'photo' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
        ];

        // `can`: admin module the tile belongs to ('any' = every admin user); `href` is optional.
        $tiles = [
            ['label' => 'Total Bookings', 'value' => number_format($stats['total_bookings']), 'sub' => $stats['today_bookings'].' today', 'can' => 'bookings', 'icon' => 'calendar', 'href' => $bookingsUrl()],
            ['label' => 'Revenue (paid)', 'value' => npr($stats['revenue']), 'sub' => npr($stats['revenue_month']).' this month', 'can' => 'reports', 'icon' => 'banknotes', 'href' => $user->isSuperAdmin() ? route('dashboard.revenue.index') : null],
            ['label' => 'Pending Payments', 'value' => number_format($stats['pending_payments']), 'sub' => npr($stats['pending_amount']).' due', 'can' => 'bookings', 'icon' => 'clock', 'href' => $bookingsUrl(['payment' => 'unpaid'])],
            ['label' => 'Confirmed / Ticketed', 'value' => number_format($stats['confirmed']), 'sub' => $stats['tickets_issued'].' tickets issued', 'can' => 'bookings', 'icon' => 'check', 'href' => $bookingsUrl(['status' => 'confirmed'])],
            ['label' => 'Cancelled', 'value' => number_format($stats['cancelled']), 'sub' => 'incl. refunded', 'can' => 'bookings', 'icon' => 'x', 'href' => $bookingsUrl(['status' => 'cancelled'])],
            ['label' => 'Customers', 'value' => number_format($stats['customers']), 'sub' => 'unique travelers', 'can' => 'customers', 'icon' => 'users', 'href' => route('dashboard.customers.index')],
            ['label' => 'Active Packages', 'value' => number_format($stats['packages']), 'sub' => 'tours, treks & activities', 'can' => 'catalog', 'icon' => 'map', 'href' => route('dashboard.tour-packages.index')],
            ['label' => 'Website Visitors', 'value' => number_format($stats['visitors_month']), 'sub' => $stats['visitors_today'].' today · '.number_format($stats['page_views_month']).' page views', 'can' => 'any', 'icon' => 'eye', 'href' => null],
        ];
        $tiles = array_filter($tiles, fn ($t) => $t['can'] === 'any' || $user->canAccessAdmin($t['can']));

        // Work queues: only shown while there is something waiting.
        $attention = array_filter([
            $stats['new_inquiries'] && $user->canAccessAdmin('inquiries')
                ? ['count' => $stats['new_inquiries'], 'label' => 'New '.Str::plural('inquiry', $stats['new_inquiries']), 'hint' => 'Waiting for a reply', 'icon' => 'inbox', 'href' => route('dashboard.inquiries.index', ['status' => 'new'])] : null,
            $stats['pending_payments'] && $user->canAccessAdmin('bookings')
                ? ['count' => $stats['pending_payments'], 'label' => 'Pending '.Str::plural('payment', $stats['pending_payments']), 'hint' => npr($stats['pending_amount']).' due', 'icon' => 'clock', 'href' => $bookingsUrl(['payment' => 'unpaid'])] : null,
            $pendingVendors
                ? ['count' => $pendingVendors, 'label' => Str::plural('Vendor', $pendingVendors).' awaiting approval', 'hint' => 'Review and approve', 'icon' => 'building', 'href' => route('dashboard.vendors.index', ['status' => 'pending'])] : null,
        ]);

        $quickActions = array_filter([
            ['New tour package', 'dashboard.tour-packages.create', 'catalog', 'map'],
            ['Add vehicle', 'dashboard.vehicles.create', 'catalog', 'truck'],
            ['Add bus route', 'dashboard.bus-routes.create', 'catalog', 'truck'],
            ['New offer / coupon', 'dashboard.offers.create', 'marketing', 'tag'],
            ['Write blog post', 'dashboard.blog-posts.create', 'content', 'pencil'],
            ['Upload photos', 'dashboard.gallery.index', 'content', 'photo'],
        ], fn ($action) => $user->canAccessAdmin($action[2]));

        $trendMax = max(1, $trend->max('value'));
        $serviceMax = max(1, $serviceRows->max('bookings'));
        $card = 'rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10';
        $sectionTitle = 'text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400';
    @endphp

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Welcome -->
            <div class="overflow-hidden rounded-xl bg-gradient-to-r from-[#0047ab] to-[#00aeef] shadow-sm">
                <div class="px-5 py-6 sm:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4 min-w-0">
                        <img src="{{ $user->profile_photo_url }}" alt="" class="hidden sm:block h-14 w-14 shrink-0 rounded-full object-cover ring-2 ring-white/70">
                        <div class="min-w-0">
                            <h3 class="text-white text-xl sm:text-2xl font-semibold truncate">Namaste, {{ $user->name }} 👋</h3>
                            <p class="mt-1 text-sm sm:text-base text-blue-100">{{ now()->format('l, F j') }} · {{ $stats['today_bookings'] }} new {{ Str::plural('booking', $stats['today_bookings']) }} today</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($user->canAccessAdmin('bookings'))
                            <a href="{{ $bookingsUrl() }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-[#0047ab] shadow hover:bg-blue-50">Manage bookings</a>
                        @endif
                        <a href="{{ route('home') }}" target="_blank" class="rounded-lg bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/25">View website ↗</a>
                    </div>
                </div>
            </div>

            <!-- Needs attention -->
            @if ($attention)
                <section>
                    <h3 class="{{ $sectionTitle }}">Needs your attention</h3>
                    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($attention as $item)
                            <a href="{{ $item['href'] }}" class="{{ $card }} group flex items-center gap-4 border-l-4 border-amber-400 p-4 transition hover:shadow-md">
                                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-400/10 dark:text-amber-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] }}" /></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100"><span class="text-lg">{{ number_format($item['count']) }}</span> {{ $item['label'] }}</span>
                                    <span class="block truncate text-xs text-gray-500 dark:text-gray-400">{{ $item['hint'] }}</span>
                                </span>
                                <span class="text-gray-400 transition group-hover:translate-x-0.5 group-hover:text-gray-600 dark:group-hover:text-gray-200" aria-hidden="true">→</span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Quick actions -->
            @if ($quickActions)
                <section>
                    <h3 class="{{ $sectionTitle }}">Quick actions</h3>
                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($quickActions as [$label, $route, $module, $icon])
                            <a href="{{ route($route) }}" class="{{ $card }} flex flex-col items-center gap-2 px-3 py-4 text-center text-sm font-medium text-gray-700 transition hover:text-indigo-600 hover:ring-indigo-300 dark:text-gray-200 dark:hover:text-indigo-400">
                                <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$icon] }}" /></svg>
                                </span>
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Overview -->
            <section>
                <h3 class="{{ $sectionTitle }}">Overview</h3>
                <div class="mt-3 grid grid-cols-2 gap-4 lg:grid-cols-4">
                    @foreach ($tiles as $tile)
                        <{{ $tile['href'] ? 'a' : 'div' }} @if ($tile['href']) href="{{ $tile['href'] }}" @endif
                            class="{{ $card }} block p-4 sm:p-5 {{ $tile['href'] ? 'transition hover:shadow-md hover:ring-indigo-300 dark:hover:ring-indigo-400/50' : '' }}">
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">{{ $tile['label'] }}</span>
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-[#0047ab] dark:bg-blue-400/10 dark:text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$tile['icon']] }}" /></svg>
                                </span>
                            </div>
                            <div class="mt-2 break-words text-xl sm:text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $tile['value'] }}</div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $tile['sub'] }}</div>
                        </{{ $tile['href'] ? 'a' : 'div' }}>
                    @endforeach
                </div>
            </section>

            <!-- Trends -->
            <section>
                <h3 class="{{ $sectionTitle }}">Booking trends</h3>
                <div class="mt-3 grid grid-cols-1 gap-5 lg:grid-cols-3">
                    <!-- Daily bookings (single series: title names it, no legend) -->
                    <figure class="lg:col-span-2 {{ $card }} p-5 sm:p-6" x-data="{ table: false }">
                        <figcaption class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Travel bookings — last 14 days</span>
                            <button type="button" @click="table = !table" class="text-xs font-medium text-indigo-600 dark:text-indigo-400" x-text="table ? 'Show chart' : 'Show table'"></button>
                        </figcaption>
                        <div x-show="!table" class="mt-6 flex h-44 items-end gap-1.5 border-b border-gray-200 dark:border-gray-700" role="img" aria-label="Bar chart of travel bookings per day for the last 14 days">
                            @foreach ($trend as $day)
                                <div class="group relative flex h-full flex-1 items-end justify-center">
                                    <div class="w-full max-w-[26px] rounded-t-[4px] bg-[#2a78d6] transition group-hover:brightness-110 dark:bg-[#3987e5]" style="height: {{ $day['value'] ? max(4, round($day['value'] / $trendMax * 100)) : 0 }}%"></div>
                                    <div class="pointer-events-none absolute bottom-full mb-1 hidden whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-xs text-white shadow group-hover:block">{{ $day['label'] }}: <strong>{{ $day['value'] }}</strong> booking{{ $day['value'] === 1 ? '' : 's' }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div x-show="!table" class="mt-2 flex gap-1.5">
                            @foreach ($trend as $day)
                                <span class="flex-1 text-center text-[11px] text-gray-500 dark:text-gray-400">
                                    <span class="hidden sm:inline">{{ $loop->first || $loop->last || $loop->index % 3 === 0 ? $day['label'] : '' }}</span>
                                    <span class="sm:hidden">{{ $loop->first || $loop->last || $loop->index % 3 === 0 ? $day['short'] : '' }}</span>
                                </span>
                            @endforeach
                        </div>
                        <table x-show="table" x-cloak class="mt-4 w-full text-sm">
                            <thead><tr class="text-left text-xs text-gray-500"><th class="py-1">Date</th><th class="py-1 text-right">Bookings</th></tr></thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($trend as $day)<tr><td class="py-1 text-gray-700 dark:text-gray-200">{{ $day['label'] }}</td><td class="py-1 text-right text-gray-900 dark:text-gray-100">{{ $day['value'] }}</td></tr>@endforeach
                            </tbody>
                        </table>
                    </figure>

                    <!-- Bookings by service (magnitude per category, one hue, direct-labeled) -->
                    <figure class="{{ $card }} p-5 sm:p-6">
                        <figcaption class="text-sm font-semibold text-gray-900 dark:text-gray-100">Bookings by service</figcaption>
                        <ul class="mt-5 space-y-3.5">
                            @foreach ($serviceRows as $row)
                                <li class="group relative">
                                    <div class="flex items-center justify-between text-sm">
                                        @if ($row['url'])<a href="{{ $row['url'] }}" class="text-gray-700 hover:text-indigo-600 dark:text-gray-200">{{ $row['label'] }}</a>@else<span class="text-gray-700 dark:text-gray-200">{{ $row['label'] }}</span>@endif
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $row['bookings'] }}</span>
                                    </div>
                                    <div class="mt-1 h-2 rounded-full bg-gray-100 dark:bg-gray-700">
                                        <div class="h-2 rounded-full bg-[#2a78d6] dark:bg-[#3987e5]" style="width: {{ $row['bookings'] ? max(2, round($row['bookings'] / $serviceMax * 100)) : 0 }}%"></div>
                                    </div>
                                    @if ($row['revenue'] !== null)
                                        <div class="pointer-events-none absolute right-0 top-full z-10 mt-1 hidden whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-xs text-white shadow group-hover:block">{{ $row['bookings'] }} bookings · {{ npr($row['revenue']) }} paid</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </figure>
                </div>
            </section>

            <!-- Booking lists -->
            @if ($user->canAccessAdmin('bookings'))
                <section>
                    <h3 class="{{ $sectionTitle }}">Bookings</h3>
                    <div class="mt-3 grid grid-cols-1 gap-5 lg:grid-cols-2">
                        @foreach ([
                            ['Latest bookings', $recent, 'No bookings yet — new ones will appear here.'],
                            ['Travelling in the next 7 days', $upcoming, 'No one is travelling in the next 7 days.'],
                        ] as [$title, $list, $empty])
                            <div class="{{ $card }}">
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-5 sm:px-6 py-4">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h4>
                                    <a href="{{ $bookingsUrl() }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400">View all →</a>
                                </div>
                                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @forelse ($list as $booking)
                                        <li>
                                            <a href="{{ route('dashboard.service-bookings.show', $booking) }}" class="flex items-center gap-3 px-5 sm:px-6 py-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-200" aria-hidden="true">{{ Str::upper(Str::substr($booking->full_name, 0, 1)) }}</span>
                                                <div class="min-w-0 flex-1">
                                                    <p class="truncate font-medium text-gray-900 dark:text-gray-100">{{ $booking->full_name }} <span class="font-normal text-gray-500">· {{ $booking->service_label }}</span></p>
                                                    <p class="truncate text-xs text-gray-500">{{ $booking->reference }} · {{ $booking->title }}</p>
                                                </div>
                                                <div class="shrink-0 text-right">
                                                    <p class="text-xs text-gray-500">{{ $booking->travel_date?->format('M d') }}</p>
                                                    <x-admin.status-badge :status="$booking->status" class="mt-0.5" />
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="px-6 py-10 text-center text-sm text-gray-400">{{ $empty }}</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
