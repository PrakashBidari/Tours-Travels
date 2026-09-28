<x-account-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('My Trips') }}</h2>
    </x-slot>

    @php
        $statusStyles = [
            'confirmed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400',
            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400',
            'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-400',
            'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="Nothing here yet." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Property</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-filter data-filter-label="Status">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($bookings as $booking)
                                @php
                                    $isUpcoming = in_array($booking->status, ['pending', 'confirmed'], true)
                                        && $booking->check_in->greaterThanOrEqualTo(now()->startOfDay());
                                @endphp
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->property->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->property->city }}, {{ $booking->property->country }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $booking->check_in->format('M d, Y') }} – {{ $booking->check_out->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $booking->reference }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $booking->currency }} {{ number_format($booking->total_price, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles[$booking->status] }}">{{ ucfirst($booking->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        @if ($isUpcoming)
                                            <form method="POST" action="{{ route('account.trips.destroy', $booking) }}"
                                                data-confirm="Delete your trip to &quot;{{ $booking->property->name }}&quot; ({{ $booking->check_in->format('M d, Y') }})? This cannot be undone."
                                                data-confirm-title="Delete this trip?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
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
</x-account-layout>
