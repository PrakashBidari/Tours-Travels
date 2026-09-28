<x-account-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Confirm & Book') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center gap-2 text-xs font-medium text-gray-400">
                <span class="text-gray-400">1. Your details</span>
                <span>&rarr;</span>
                <span class="text-brand-600">2. Confirm &amp; book</span>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($items as $item)
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['property']->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item['property']->city }}, {{ $item['property']->country }} &middot; {{ $item['check_in']->format('M d, Y') }} – {{ $item['check_out']->format('M d, Y') }} &middot; {{ $item['guests'] }} guest(s) &middot; {{ $item['nights'] }} night(s)</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 shrink-0">{{ $item['property']->currency }} {{ number_format($item['subtotal'], 2) }}</span>
                    </div>
                @endforeach
                <div class="px-6 py-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Total</span>
                    <span class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $items->first()['property']->currency }} {{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Your contact details</h3>
                    <a href="{{ route('account.booking.details') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">Edit</a>
                </div>

                <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Name</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Email</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Phone</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $user->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase tracking-wide">Address</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $user->address }}, {{ $user->city }}, {{ $user->country }} {{ $user->postal_code }}</dd>
                    </div>
                </dl>
            </div>

            <form method="POST" action="{{ route('account.booking.confirm.store') }}" class="flex justify-end">
                @csrf
                <x-button class="px-6 py-2.5">Confirm &amp; book</x-button>
            </form>
        </div>
    </div>
</x-account-layout>
