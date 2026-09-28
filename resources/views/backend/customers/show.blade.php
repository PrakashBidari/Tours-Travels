<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ $user?->name ?? $bookings->first()?->full_name }}</h2>
            <a href="{{ route('dashboard.customers.index') }}" class="text-sm text-gray-500">&larr; Customers</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="space-y-6">
                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 text-sm space-y-3">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Profile</h3>
                    <p><span class="text-gray-500">Email:</span> <a href="mailto:{{ $email }}" class="text-indigo-600">{{ $email }}</a></p>
                    <p><span class="text-gray-500">Phone:</span> <span class="text-gray-900 dark:text-gray-100">{{ $user?->phone ?? $bookings->first()?->phone ?? '—' }}</span></p>
                    <p><span class="text-gray-500">Nationality:</span> <span class="text-gray-900 dark:text-gray-100">{{ $bookings->pluck('nationality')->filter()->first() ?? '—' }}</span></p>
                    <p><span class="text-gray-500">Account:</span> <span class="text-gray-900 dark:text-gray-100">{{ $user ? 'Registered '.$user->created_at->format('M Y') : 'Guest (no account)' }}</span></p>
                    <p><span class="text-gray-500">Newsletter:</span> <span class="text-gray-900 dark:text-gray-100">{{ $subscribed ? 'Subscribed' : 'Not subscribed' }}</span></p>
                    <p><span class="text-gray-500">Passports on file:</span> <span class="font-mono text-gray-900 dark:text-gray-100">{{ $passports->implode(', ') ?: '—' }}</span></p>
                </div>
                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 text-sm">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Travel preferences</h3>
                    <ul class="mt-3 space-y-2">
                        @forelse ($preferences as $service => $count)
                            <li class="flex justify-between"><span class="text-gray-600 dark:text-gray-300">{{ $service }}</span><span class="font-medium text-gray-900 dark:text-gray-100">{{ $count }}</span></li>
                        @empty
                            <li class="text-gray-400">No bookings yet.</li>
                        @endforelse
                    </ul>
                    <p class="mt-4 border-t border-gray-100 dark:border-gray-700 pt-3 text-gray-500">Total paid: <strong class="text-gray-900 dark:text-gray-100">{{ npr($bookings->where('payment_status', 'paid')->sum('total')) }}</strong></p>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-x-auto">
                    <h3 class="border-b border-gray-100 dark:border-gray-700 px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Booking & payment history</h3>
                    <table class="min-w-full text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($bookings as $booking)
                                <tr>
                                    <td class="px-6 py-3"><a href="{{ route('dashboard.service-bookings.show', $booking) }}" class="font-mono font-medium text-indigo-600">{{ $booking->reference }}</a><div class="text-xs text-gray-500">{{ $booking->service_label }} · {{ Str::limit($booking->title, 40) }}</div></td>
                                    <td class="px-6 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $booking->travel_date?->format('M d, Y') }}</td>
                                    <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ npr($booking->total) }}</td>
                                    <td class="px-6 py-3"><x-admin.status-badge :status="$booking->status" /></td>
                                    <td class="px-6 py-3"><x-admin.status-badge :status="$booking->payment_status" /></td>
                                </tr>
                            @empty
                                <tr><td class="px-6 py-8 text-center text-gray-400">No travel bookings.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($hotelBookings->isNotEmpty())
                    <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-x-auto">
                        <h3 class="border-b border-gray-100 dark:border-gray-700 px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">Hotel bookings</h3>
                        <table class="min-w-full text-sm">
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($hotelBookings as $booking)
                                    <tr>
                                        <td class="px-6 py-3 font-mono text-gray-900 dark:text-gray-100">{{ $booking->reference }}</td>
                                        <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $booking->property?->name }}</td>
                                        <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $booking->check_in->format('M d') }} – {{ $booking->check_out->format('M d, Y') }}</td>
                                        <td class="px-6 py-3"><x-admin.status-badge :status="$booking->status" /></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
