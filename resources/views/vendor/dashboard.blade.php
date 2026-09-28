<x-vendor-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Vendor Dashboard') }}</h2>
    </x-slot>

    @php
        $vendorStatus = Auth::user()->vendor_status;
        $statusBanner = match ($vendorStatus) {
            'approved' => ['color' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400', 'label' => 'Approved', 'desc' => 'Your vendor account is approved. You can post and manage packages.'],
            'rejected' => ['color' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400', 'label' => 'Rejected', 'desc' => 'Your vendor application was not approved. Contact support for details.'],
            default => ['color' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400', 'label' => 'Pending Review', 'desc' => "You're all set \u{2014} our team is reviewing your application. You'll be notified by email once approved."],
        };
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 flex items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusBanner['color'] }}">
                        Vendor account: {{ $statusBanner['label'] }}
                    </span>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $statusBanner['desc'] }}</p>
                </div>
            </div>

            @if ($stats)
                <div class="overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 shadow-sm">
                    <div class="px-6 py-8 sm:px-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-white text-2xl font-semibold">Welcome back, {{ Auth::user()->name }} 👋</h3>
                            <p class="mt-1 text-indigo-100">Total payout to date: ${{ number_format($stats['total_payout'], 2) }}</p>
                        </div>
                        <a href="{{ route('vendor.packages.create') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50 transition">
                            + New Package
                        </a>
                    </div>
                </div>

                @php
                    $tiles = [
                        ['label' => 'My Listings', 'value' => $stats['total_listings']],
                        ['label' => 'Pending Approval', 'value' => $stats['pending_listings']],
                        ['label' => 'Total Bookings', 'value' => $stats['total_bookings']],
                        ['label' => 'Upcoming Check-ins', 'value' => $stats['upcoming_checkins']],
                    ];
                    $statusStyles = [
                        'confirmed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
                        'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400',
                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
                    ];
                @endphp

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($tiles as $stat)
                        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</span>
                            <div class="mt-3 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stat['value']) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent Bookings</h4>
                        <a href="{{ route('vendor.bookings.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/40">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Guest</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Package</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Check-in</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($recentBookings as $booking)
                                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->user->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $booking->property->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $booking->check_in->format('M d, Y') }}</td>
                                        <td class="px-6 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles[$booking->status] }}">{{ ucfirst($booking->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No bookings yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-vendor-layout>
