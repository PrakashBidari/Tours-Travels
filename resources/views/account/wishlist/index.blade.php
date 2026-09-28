<x-account-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Wishlist') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($properties as $property)
                    <div class="relative" data-wishlist-card>
                        <x-frontend.property-card :property="$property" />
                        <button
                            type="button"
                            data-url="{{ route('account.wishlist.toggle', $property) }}"
                            onclick="window.toggleWishlist(this)"
                            class="absolute top-3 right-3 z-20 h-8 w-8 flex items-center justify-center rounded-full bg-white/90 text-red-500 shadow"
                            title="Remove from wishlist"
                        >♥</button>
                    </div>
                @empty
                    <p class="col-span-full text-center text-sm text-gray-400 py-12">Your wishlist is empty. Browse packages and tap the heart to save them here.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-account-layout>
