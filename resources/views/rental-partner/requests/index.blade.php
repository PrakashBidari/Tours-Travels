<x-rental-partner-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Available Requests') }}</h2>
    </x-slot>

    @php
        $vehicleTypeLabels = [
            'taxi' => 'Taxi',
            'bike' => 'Bike',
            'other' => 'Other',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No ride requests available right now." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Traveller</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-filter data-filter-label="Vehicle">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Pickup</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($requests as $request)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $request->traveller->name }}</div>
                                        @if ($request->traveller->phone ?? false)
                                            <div class="text-xs text-gray-400">{{ $request->traveller->phone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $vehicleTypeLabels[$request->vehicle_type] ?? ucfirst($request->vehicle_type) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $request->pickup_location }} → {{ $request->dropoff_location }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $request->pickup_date->format('M d, Y') }} {{ \Illuminate\Support\Carbon::parse($request->pickup_time)->format('g:i A') }}</td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <form method="POST" action="{{ route('rental-partner.requests.claim', $request) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Claim</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('app-notification', (event) => {
            if (event.detail?.type === 'vehicle_rental_request') {
                window.location.reload();
            }
        });
    </script>
</x-rental-partner-layout>
