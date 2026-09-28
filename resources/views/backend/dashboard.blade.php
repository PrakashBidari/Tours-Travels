<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ Auth::user()->isSuperAdmin() ? 'Ram Tours Admin Dashboard' : (Auth::user()->staff_role_label.' Dashboard') }}
        </h2>
    </x-slot>

    @php
        $tiles = [
            ['label' => 'Total Bookings', 'value' => number_format($stats['total_bookings']), 'sub' => $stats['today_bookings'].' today', 'can' => 'bookings'],
            ['label' => 'Revenue (paid)', 'value' => npr($stats['revenue']), 'sub' => npr($stats['revenue_month']).' this month', 'can' => 'reports'],
            ['label' => 'Pending Payments', 'value' => number_format($stats['pending_payments']), 'sub' => npr($stats['pending_amount']).' due', 'can' => 'bookings'],
            ['label' => 'Confirmed / Ticketed', 'value' => number_format($stats['confirmed']), 'sub' => $stats['tickets_issued'].' tickets issued', 'can' => 'bookings'],
            ['label' => 'Cancelled', 'value' => number_format($stats['cancelled']), 'sub' => 'incl. refunded', 'can' => 'bookings'],
            ['label' => 'Customers', 'value' => number_format($stats['customers']), 'sub' => 'unique travelers', 'can' => 'customers'],
            ['label' => 'Active Packages', 'value' => number_format($stats['packages']), 'sub' => 'tours, treks & activities', 'can' => 'catalog'],
            ['label' => 'Website Visitors', 'value' => number_format($stats['visitors_month']), 'sub' => $stats['visitors_today'].' today · '.number_format($stats['page_views_month']).' page views', 'can' => 'any'],
        ];
        $tiles = array_filter($tiles, fn ($t) => $t['can'] === 'any' || Auth::user()->canAccessAdmin($t['can']));
        $trendMax = max(1, $trend->max('value'));
        $serviceMax = max(1, $serviceRows->max('bookings'));
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="overflow-hidden rounded-xl bg-gradient-to-r from-[#0047ab] to-[#00aeef] shadow-sm">
                <div class="px-6 py-7 sm:px-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-white text-2xl font-semibold">Namaste, {{ Auth::user()->name }} 👋</h3>
                        <p class="mt-1 text-blue-100">Here's what's happening at Ram Tours & Travel today — {{ now()->format('l, M d') }}.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($stats['new_inquiries'] && Auth::user()->canAccessAdmin('inquiries'))
                            <a href="{{ route('dashboard.inquiries.index', ['status' => 'new']) }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-[#0047ab] shadow hover:bg-blue-50">{{ $stats['new_inquiries'] }} new inquiries</a>
                        @endif
                        @if ($pendingVendors)
                            <a href="{{ route('dashboard.vendors.index', ['status' => 'pending']) }}" class="rounded-lg bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/25">{{ $pendingVendors }} vendor(s) awaiting approval</a>
                        @endif
                        <a href="{{ route('home') }}" target="_blank" class="rounded-lg bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/40 hover:bg-white/25">View website ↗</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tiles as $tile)
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $tile['label'] }}</span>
                        <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $tile['value'] }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $tile['sub'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <!-- Daily bookings (single series: title names it, no legend) -->
                <figure class="lg:col-span-2 rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10" x-data="{ table: false }">
                    <figcaption class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Travel bookings — last 14 days</span>
                        <button type="button" @click="table = !table" class="text-xs font-medium text-indigo-600" x-text="table ? 'Show chart' : 'Show table'"></button>
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
                            <span class="flex-1 text-center text-[11px] text-gray-500 dark:text-gray-400">{{ $loop->first || $loop->last || $loop->index % 3 === 0 ? $day['label'] : '' }}</span>
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
                <figure class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
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

            @if (Auth::user()->canAccessAdmin('bookings'))
                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    @foreach (['Latest bookings' => $recent, 'Travelling in the next 7 days' => $upcoming] as $title => $list)
                        <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h4>
                                <a href="{{ route('dashboard.service-bookings.index') }}" class="text-xs font-medium text-indigo-600">View all</a>
                            </div>
                            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($list as $booking)
                                    <li>
                                        <a href="{{ route('dashboard.service-bookings.show', $booking) }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate font-medium text-gray-900 dark:text-gray-100">{{ $booking->full_name }} <span class="font-normal text-gray-500">· {{ $booking->service_label }}</span></p>
                                                <p class="truncate text-xs text-gray-500">{{ $booking->reference }} · {{ $booking->title }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ $booking->travel_date?->format('M d') }}</p>
                                                <x-admin.status-badge :status="$booking->status" class="mt-0.5" />
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-6 py-8 text-center text-sm text-gray-400">Nothing here yet.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                @foreach ([
                    ['New tour package', 'dashboard.tour-packages.create', 'catalog'],
                    ['Add vehicle', 'dashboard.vehicles.create', 'catalog'],
                    ['Add bus route', 'dashboard.bus-routes.create', 'catalog'],
                    ['New offer / coupon', 'dashboard.offers.create', 'marketing'],
                    ['Write blog post', 'dashboard.blog-posts.create', 'content'],
                    ['Upload photos', 'dashboard.gallery.index', 'content'],
                ] as [$label, $route, $module])
                    @if (Auth::user()->canAccessAdmin($module))
                        <a href="{{ route($route) }}" class="rounded-xl bg-white dark:bg-gray-800 px-4 py-3 text-center text-sm font-medium text-indigo-600 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 hover:ring-indigo-300">+ {{ $label }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
