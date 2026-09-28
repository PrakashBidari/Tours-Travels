<x-account-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Travel Bookings') }}</h2>
            <a href="{{ route('tours.index') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white">{{ __('Book a new trip') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/40 text-left text-xs uppercase text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">{{ __('Booking') }}</th>
                            <th class="px-6 py-3">{{ __('Travel date') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Total') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Payment') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($bookings as $booking)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->service_label }} · <span class="font-mono">{{ $booking->reference }}</span></p>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $booking->travel_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $booking->total > 0 ? npr($booking->total) : __('Awaiting quote') }}</td>
                                <td class="px-6 py-4"><x-admin.status-badge :status="$booking->status" /></td>
                                <td class="px-6 py-4"><x-admin.status-badge :status="$booking->payment_status" /></td>
                                <td class="px-6 py-4 text-right whitespace-nowrap space-x-3">
                                    @if ($booking->isPayable())<a href="{{ URL::signedRoute('booking.pay', $booking) }}" class="text-xs font-semibold text-emerald-600">{{ __('Pay now') }}</a>@endif
                                    <a href="{{ \App\Services\BookingService::confirmationUrl($booking) }}" class="text-xs font-medium text-indigo-600">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('No travel bookings yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-account-layout>
