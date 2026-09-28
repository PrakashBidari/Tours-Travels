<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $route->from_city }} → {{ $route->to_city }} · {{ $route->bus_name }}</h2>
                <p class="text-sm text-gray-500">{{ $route->operator }} · departs {{ $route->departure_label }}</p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2 text-sm">
                    <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                </form>
                <button onclick="window.print()" class="rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-700 ring-1 ring-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700">Print manifest</button>
                <a href="{{ route('dashboard.bus-routes.index', ['date' => $date]) }}" class="text-sm text-gray-500">&larr; Routes</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[auto_1fr] gap-6">
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 h-fit">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $reservations->count() }} / {{ $route->total_seats }} seats booked · {{ \Carbon\Carbon::parse($date)->format('D, M d, Y') }}</p>
                <div class="mt-4 space-y-1.5">
                    @foreach ($route->seatRows() as $row)
                        <div class="flex gap-1.5">
                            @foreach ($row as $seat)
                                @if ($seat === null)
                                    <span class="w-5"></span>
                                @else
                                    <span title="{{ $reservations[$seat]->booking->full_name ?? 'Available' }}" class="flex h-9 w-9 items-center justify-center rounded-md text-[10px] font-semibold {{ isset($reservations[$seat]) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300' }}">{{ $seat }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-left text-xs uppercase text-gray-500">
                        <tr><th class="px-4 py-3">Seat</th><th class="px-4 py-3">Passenger</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Booking</th><th class="px-4 py-3">Payment</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($reservations->sortKeys(SORT_NATURAL) as $seat => $reservation)
                            <tr>
                                <td class="px-4 py-2 font-semibold text-gray-900 dark:text-gray-100">{{ $seat }}</td>
                                <td class="px-4 py-2 text-gray-700 dark:text-gray-200">{{ $reservation->booking->full_name }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $reservation->booking->phone }}</td>
                                <td class="px-4 py-2"><a href="{{ route('dashboard.service-bookings.show', $reservation->booking) }}" class="font-mono text-indigo-600">{{ $reservation->booking->reference }}</a></td>
                                <td class="px-4 py-2"><x-admin.status-badge :status="$reservation->booking->payment_status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No seats booked for this date yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
