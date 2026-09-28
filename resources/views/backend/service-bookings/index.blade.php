<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Travel Bookings</h2>
            <a href="{{ route('dashboard.service-bookings.export', request()->query()) }}" class="rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700">Export CSV</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.service-bookings.index', request()->except(['service', 'page'])) }}"
                    class="px-3 py-1.5 rounded-full text-sm font-medium {{ ! request('service') ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">All ({{ $counts->sum() }})</a>
                @foreach (config('travel.service_types') as $key => $label)
                    @continue($key === 'hotel')
                    <a href="{{ route('dashboard.service-bookings.index', array_merge(request()->except('page'), ['service' => $key])) }}"
                        class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('service') === $key ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">{{ $label }} ({{ $counts[$key] ?? 0 }})</a>
                @endforeach
                <a href="{{ route('dashboard.bookings.index') }}" class="px-3 py-1.5 rounded-full text-sm font-medium bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700">Hotel bookings &rarr;</a>
            </div>

            <form method="GET" class="grid grid-cols-2 md:grid-cols-6 gap-3 rounded-xl bg-white dark:bg-gray-800 p-4 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                @if (request('service'))<input type="hidden" name="service" value="{{ request('service') }}">@endif
                <input name="q" value="{{ request('q') }}" placeholder="Ref, name, email, phone, PNR…" class="col-span-2 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                    <option value="">Any status</option>
                    @foreach (config('travel.booking_statuses') as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach
                </select>
                <select name="payment" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                    <option value="">Any payment</option>
                    @foreach (config('travel.payment_statuses') as $status)<option value="{{ $status }}" @selected(request('payment') === $status)>{{ ucfirst($status) }}</option>@endforeach
                </select>
                <input type="date" name="from" value="{{ request('from') }}" title="Booked from" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                <div class="flex gap-2">
                    <input type="date" name="to" value="{{ request('to') }}" title="Booked to" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                    <button class="rounded-md bg-indigo-600 px-3 text-sm font-medium text-white">Filter</button>
                </div>
            </form>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40">
                        <tr class="text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                            <th class="px-4 py-3">Booking</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3">Travel date</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Payment</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($bookings as $booking)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                <td class="px-4 py-3">
                                    <a href="{{ route('dashboard.service-bookings.show', $booking) }}" class="font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $booking->reference }}</a>
                                    <div class="text-xs text-gray-500 dark:text-gray-400"><span class="font-medium">{{ $booking->service_label }}</span> · {{ Str::limit($booking->title, 45) }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $booking->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-200">{{ $booking->full_name }}<div class="text-xs text-gray-500">{{ $booking->phone }}</div></td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $booking->travel_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $booking->total > 0 ? npr($booking->total) : 'Quote' }}</td>
                                <td class="px-4 py-3"><x-admin.status-badge :status="$booking->status" /></td>
                                <td class="px-4 py-3"><x-admin.status-badge :status="$booking->payment_status" />@if ($booking->payment_method)<div class="mt-0.5 text-[11px] text-gray-400">{{ $booking->payment_method_label }}</div>@endif</td>
                                <td class="px-4 py-3 text-right"><a href="{{ route('dashboard.service-bookings.show', $booking) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Manage</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $bookings->links() }}
        </div>
    </div>
</x-app-layout>
