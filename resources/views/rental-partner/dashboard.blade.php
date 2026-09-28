<x-rental-partner-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Rental Partner Dashboard') }}</h2>
    </x-slot>

    @php
        $partnerStatus = Auth::user()->vendor_status;
        $statusBanner = match ($partnerStatus) {
            'approved' => ['color' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400', 'label' => 'Approved', 'desc' => 'Your rental partner account is approved. You can claim and fulfill ride requests.'],
            'rejected' => ['color' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400', 'label' => 'Rejected', 'desc' => 'Your rental partner application was not approved. Contact support for details.'],
            default => ['color' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400', 'label' => 'Pending Review', 'desc' => "You're all set \u{2014} our team is reviewing your application. You'll be notified by email once approved."],
        };
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 flex items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusBanner['color'] }}">
                        Rental partner account: {{ $statusBanner['label'] }}
                    </span>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $statusBanner['desc'] }}</p>
                </div>
            </div>

            @if ($stats)
                <div class="overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 shadow-sm">
                    <div class="px-6 py-8 sm:px-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-white text-2xl font-semibold">Welcome back, {{ Auth::user()->name }} 👋</h3>
                            <p class="mt-1 text-indigo-100">{{ $stats['available_count'] }} ride request(s) available to claim right now.</p>
                        </div>
                        <a href="{{ route('rental-partner.requests.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50 transition">
                            View Available Requests
                        </a>
                    </div>
                </div>

                @php
                    $tiles = [
                        ['label' => 'Available Requests', 'value' => $stats['available_count']],
                        ['label' => 'Applied Pickups', 'value' => $stats['applied_count']],
                        ['label' => 'Completed Rides', 'value' => $stats['completed_count']],
                    ];
                    $vehicleTypeLabels = [
                        'taxi' => 'Taxi',
                        'bike' => 'Bike',
                        'other' => 'Other',
                    ];
                @endphp

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tiles as $stat)
                        <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</span>
                            <div class="mt-3 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stat['value']) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Recent Ride Requests</h4>
                        <a href="{{ route('rental-partner.requests.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/40">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Traveller</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vehicle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Route</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pickup</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse ($recentRequests as $request)
                                    <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $request->traveller->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $vehicleTypeLabels[$request->vehicle_type] ?? ucfirst($request->vehicle_type) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $request->pickup_location }} → {{ $request->dropoff_location }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $request->pickup_date->format('M d, Y') }} {{ \Illuminate\Support\Carbon::parse($request->pickup_time)->format('g:i A') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('rental-partner.requests.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">Claim</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No ride requests available right now.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-rental-partner-layout>
