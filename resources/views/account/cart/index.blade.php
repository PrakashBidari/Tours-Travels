<x-account-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('My Cart') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($items as $item)
                    <div class="px-6 py-4 flex items-center justify-between gap-4" data-cart-row>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->property->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->check_in->format('M d, Y') }} – {{ $item->check_out->format('M d, Y') }} &middot; {{ $item->guests }} guest(s) &middot; {{ $item->nights() }} night(s)</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->property->currency }} {{ number_format($item->subtotal(), 2) }}</span>
                            <button
                                type="button"
                                data-url="{{ route('account.cart.destroy', $item) }}"
                                onclick="window.removeCartItem(this)"
                                class="text-xs font-medium text-red-600 hover:text-red-700"
                            >Remove</button>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-12 text-center text-sm text-gray-400">Your cart is empty.</p>
                @endforelse
            </div>

            <p data-cart-empty class="hidden px-6 py-12 text-center text-sm text-gray-400">Your cart is empty.</p>

            @if ($items->isNotEmpty())
                <div class="flex items-center justify-between rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 px-6 py-4" data-cart-summary>
                    <span class="text-sm font-medium text-gray-500" data-cart-total>Total: {{ $items->first()->property->currency }} {{ number_format($items->sum(fn ($i) => $i->subtotal()), 2) }}</span>
                    <form method="POST" action="{{ route('account.cart.checkout') }}">
                        @csrf
                        <x-button>Book now</x-button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-account-layout>
