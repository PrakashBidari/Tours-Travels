<x-account-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('My Rental Requests') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No rental requests yet." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vehicle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pickup</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-filter data-filter-label="Status">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($requests as $request)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($request->vehicle_type) }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $request->pickup_location }} &rarr; {{ $request->dropoff_location }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $request->pickup_date->format('M d, Y') }} at {{ \Carbon\Carbon::parse($request->pickup_time)->format('g:i A') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $statusStyles[$request->status] }}">{{ $statusLabels[$request->status] }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex flex-col items-end gap-2">
                                            @if ($request->status === 'claimed')
                                                <div class="flex items-center gap-3">
                                                    <form method="POST" action="{{ route('account.rental-requests.accept', $request) }}">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Accept</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('account.rental-requests.reject', $request) }}" class="flex items-center gap-2">
                                                        @csrf @method('PATCH')
                                                        <input type="text" name="reason" required placeholder="Reason (required)" class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-36" />
                                                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Reject</button>
                                                    </form>
                                                </div>
                                            @elseif ($request->status === 'accepted' && $request->rentalPartner)
                                                <div class="text-left rounded-lg bg-emerald-50 dark:bg-emerald-500/10 px-3 py-2 ring-1 ring-inset ring-emerald-600/20">
                                                    <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Tracking</p>
                                                    <p class="text-xs text-emerald-700 dark:text-emerald-400">{{ $request->rentalPartner->company_name }}</p>
                                                    <p class="text-xs text-emerald-700 dark:text-emerald-400">{{ $request->rentalPartner->company_phone }}</p>
                                                </div>
                                            @endif

                                            @if ($request->status !== 'completed')
                                                <form method="POST" action="{{ route('account.rental-requests.destroy', $request) }}"
                                                    data-confirm="Delete this rental request ({{ $request->pickup_location }} &rarr; {{ $request->dropoff_location }})? This cannot be undone."
                                                    data-confirm-title="Delete this request?"
                                                    class="flex items-center gap-2">
                                                    @csrf @method('DELETE')
                                                    <input type="text" name="reason" required placeholder="Reason (required)" class="text-xs border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-36" />
                                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                                                </form>
                                            @endif
                                        </div>
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
</x-account-layout>
