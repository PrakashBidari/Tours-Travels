<x-account-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('My Dashboard') }}</h2>
    </x-slot>

    @php
        $tiles = [
            ['label' => 'Upcoming Trips', 'value' => $stats['upcoming_trips']],
            ['label' => 'Completed Trips', 'value' => $stats['completed_trips']],
            ['label' => 'Wishlist Items', 'value' => $stats['wishlist_count']],
            ['label' => 'In Cart', 'value' => $stats['cart_count']],
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-500 shadow-sm">
                <div class="px-6 py-8 sm:px-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-white text-2xl font-semibold">Welcome back, {{ Auth::user()->name }} 👋</h3>
                        <p class="mt-1 text-indigo-100">Here's a look at your trips.</p>
                    </div>
                    <a href="{{ route('search') }}" class="inline-flex items-center justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50 transition">
                        Find your next trip
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($tiles as $stat)
                    <div class="rounded-xl bg-white dark:bg-gray-800 p-5 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</span>
                        <div class="mt-3 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stat['value'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Upcoming Trips</h4>
                    <a href="{{ route('account.trips.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">View all</a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($upcoming as $booking)
                        <div class="px-6 py-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->property->name }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->check_in->format('M d, Y') }} – {{ $booking->check_out->format('M d, Y') }}</p>
                            </div>
                            <span class="text-xs font-medium text-gray-500">{{ ucfirst($booking->status) }}</span>
                        </div>
                    @empty
                        <p class="px-6 py-8 text-center text-sm text-gray-400">No upcoming trips yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-account-layout>
