<x-site-layout :title="$property->name">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $property->name }}</h1>
                    <p class="text-gray-500">{{ $property->city }}, {{ $property->country }} &middot; <span class="capitalize">{{ $property->type }}</span></p>
                </div>

                @php
                    $galleryImages = collect($property->images)
                        ->map(fn ($image) => str_starts_with($image, 'http') ? $image : \Illuminate\Support\Facades\Storage::disk('public')->url($image))
                        ->values();
                    if ($galleryImages->isEmpty()) {
                        $galleryImages = collect([$property->main_image]);
                    }
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    @foreach ($galleryImages as $index => $image)
                        <a
                            href="{{ $image }}"
                            data-gallery="property-{{ $property->id }}"
                            class="glightbox group relative block h-32 w-full overflow-hidden rounded-lg {{ $index >= 4 ? 'hidden' : '' }} {{ $galleryImages->count() === 1 ? 'col-span-2 sm:col-span-4' : '' }}"
                        >
                            <img src="{{ $image }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $property->name }}" loading="lazy">
                            <span class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white opacity-0 group-hover:opacity-100 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0a7.5 7.5 0 10-10.6 0 7.5 7.5 0 0010.6 0zM10.5 7.5v6m-3-3h6" />
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </div>

                <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">{!! $property->description !!}</div>

                <div class="flex flex-wrap gap-2">
                    @foreach ($property->amenities as $amenity)
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-xs text-brand-700">{{ $amenity }}</span>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 p-6">
                    <div class="text-2xl font-bold text-gray-900">{{ $property->currency }} {{ number_format($property->price_per_night, 0) }}</div>
                    <div class="text-sm text-gray-500 mb-4">per night &middot; up to {{ $property->max_guests }} guests</div>

                    @auth
                        @if (Auth::user()->isUser())
                            <form method="POST" action="{{ route('account.cart.book-now') }}" class="space-y-3">
                                @csrf
                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                <div>
                                    <x-label for="check_in" value="Check-in" />
                                    <x-input id="check_in" name="check_in" type="date" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-label for="check_out" value="Check-out" />
                                    <x-input id="check_out" name="check_out" type="date" class="block mt-1 w-full" required />
                                </div>
                                <div>
                                    <x-label for="guests" value="Guests" />
                                    <x-input id="guests" name="guests" type="number" min="1" max="{{ $property->max_guests }}" value="1" class="block mt-1 w-full" required />
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
                                    Book Now
                                </button>
                                <x-button type="button" data-url="{{ route('account.cart.store') }}" onclick="window.addToCart(this)" class="w-full justify-center py-2.5">Add to cart</x-button>
                                <p data-cart-feedback class="hidden text-sm text-center"></p>
                            </form>

                            <button
                                type="button"
                                data-url="{{ route('account.wishlist.toggle', $property) }}"
                                data-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                                onclick="window.toggleWishlist(this)"
                                class="mt-3 w-full inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                <span data-wishlist-label>{{ $isWishlisted ? '♥ Remove from wishlist' : '♡ Add to wishlist' }}</span>
                            </button>
                        @else
                            <p class="text-sm text-gray-500">Only traveler accounts can book. Vendors and admins cannot make bookings.</p>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                            Sign in to book
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        @if ($popularPackages->isNotEmpty())
            <section class="mt-14">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Popular destination packages</h2>
                        <p class="mt-1 text-gray-500">Other stays travelers are loving right now.</p>
                    </div>
                    <a href="{{ route('search') }}" class="hidden sm:inline-flex text-sm font-medium text-brand-700 hover:text-brand-800">View all &rarr;</a>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($popularPackages as $package)
                        <x-frontend.property-card :property="$package" layout="grid" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-site-layout>
