<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Revenue') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <form method="GET" class="flex flex-wrap gap-3 items-end bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                </div>
                <button class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">Apply</button>

                <div class="flex-1"></div>

                <a href="{{ route('dashboard.revenue.pdf', request()->query()) }}" class="rounded-md bg-white dark:bg-gray-900 ring-1 ring-gray-300 dark:ring-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium px-4 py-2">Download PDF</a>
                <a href="{{ route('dashboard.revenue.doc', request()->query()) }}" class="rounded-md bg-white dark:bg-gray-900 ring-1 ring-gray-300 dark:ring-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium px-4 py-2">Download DOC</a>
            </form>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Bookings</span>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totals['bookings_count']) }}</div>
                </div>
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Platform Revenue</span>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totals['admin_revenue'], 2) }}</div>
                </div>
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Vendor Payouts</span>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totals['vendor_payout'], 2) }}</div>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No revenue in this period." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vendor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Bookings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Platform Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Vendor Payout</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($revenueByVendor as $row)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row->vendor?->name ?? 'Unassigned' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $row->bookings_count }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${{ number_format($row->admin_revenue, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${{ number_format($row->vendor_payout, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
