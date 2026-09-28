<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Customers</h2>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('dashboard.users.index') }}" class="font-medium text-indigo-600">{{ $registered }} registered accounts &rarr;</a>
                <a href="{{ route('dashboard.subscribers.index') }}" class="font-medium text-indigo-600">Newsletter subscribers &rarr;</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex max-w-md gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="Search name, email or phone" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                <button class="rounded-md bg-indigo-600 px-4 text-sm font-medium text-white">Search</button>
            </form>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-left text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Nationality</th><th class="px-4 py-3">Bookings</th><th class="px-4 py-3 text-right">Paid (NPR)</th><th class="px-4 py-3">Last booking</th><th class="px-4 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3"><span class="font-medium text-gray-900 dark:text-gray-100">{{ $customer->name }}</span> @if ($customer->user_id)<span class="ml-1 rounded bg-indigo-50 px-1.5 text-[10px] text-indigo-600">account</span>@endif<div class="text-xs text-gray-500">{{ $customer->email }}</div></td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $customer->phone }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $customer->nationality ?: '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $customer->bookings }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ number_format((float) $customer->paid) }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($customer->last_booking)->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('dashboard.customers.show', $customer->email) }}" class="text-xs font-medium text-indigo-600">Profile</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No customers yet — they appear here after their first booking.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $customers->links() }}
        </div>
    </div>
</x-app-layout>
