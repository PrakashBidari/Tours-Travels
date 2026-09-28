<x-rental-partner-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Applied Pickups') }}</h2>
    </x-slot>

    @php
        $vehicleTypeLabels = [
            'taxi' => 'Taxi',
            'bike' => 'Bike',
            'other' => 'Other',
        ];
        $statusStyles = [
            'claimed' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
            'accepted' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
            'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="You haven't claimed any requests yet." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Traveller</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-filter data-filter-label="Vehicle">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Pickup</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-filter data-filter-label="Status">Status</th>
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
                                    <td class="px-6 py-4 whitespace-nowrap"><span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles[$request->status] ?? '' }}">{{ ucfirst($request->status) }}</span></td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                        @if ($request->status === 'accepted')
                                            <form method="POST" action="{{ route('rental-partner.requests.complete', $request) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="text-xs font-medium text-sky-600 hover:text-sky-700">Mark completed</button>
                                            </form>
                                        @endif
                                        @if ($request->status !== 'completed')
                                            <form method="POST" action="{{ route('rental-partner.requests.unclaim', $request) }}" class="inline-flex items-center gap-1" x-data="{ open: false }">
                                                @csrf
                                                @method('DELETE')
                                                <template x-if="!open">
                                                    <button type="button" @click="open = true" class="text-xs font-medium text-red-600 hover:text-red-700">Cancel</button>
                                                </template>
                                                <template x-if="open">
                                                    <span class="inline-flex items-center gap-1">
                                                        <input type="text" name="reason" required maxlength="500" placeholder="Reason for cancelling"
                                                            class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Confirm</button>
                                                        <button type="button" @click="open = false" class="text-xs font-medium text-gray-400 hover:text-gray-600">Back</button>
                                                    </span>
                                                </template>
                                            </form>
                                        @endif
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
