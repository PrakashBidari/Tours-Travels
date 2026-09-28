@props(['property', 'layout' => 'grid', 'hideRating' => false])

@php
    $ratingColor = match (true) {
        $property->rating >= 9.0 => 'bg-brand-700',
        $property->rating >= 8.0 => 'bg-brand-600',
        $property->rating >= 7.0 => 'bg-brand-500',
        default => 'bg-gray-500',
    };
    $canBook = Auth::check() && Auth::user()->isUser();
    $isWishlisted = $canBook && Auth::user()->wishlists()->where('property_id', $property->id)->exists();
@endphp

<div class="relative h-full flex flex-col bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 overflow-hidden hover:shadow-md transition {{ $layout === 'list' ? 'sm:flex-row' : '' }}">
    {{-- Stretched link: makes the whole card (image included) clickable. Sits above the
         image but below the wishlist button (z-20), which stops its own click via
         event.preventDefault() in its onclick handler. --}}
    <a href="{{ route('property.show', $property) }}" class="absolute inset-0 z-10" aria-label="{{ $property->name }}"></a>

    <div class="relative {{ $layout === 'list' ? 'sm:w-72 shrink-0' : '' }}">
        <img
            src="{{ $property->main_image }}"
            alt="{{ $property->name }}"
            class="w-full object-cover {{ $layout === 'list' ? 'h-48 sm:h-full' : 'h-48' }}"
            loading="lazy"
        >
        @if ($property->is_featured)
            <span class="absolute top-3 left-3 inline-flex items-center rounded-full bg-white/90 px-2.5 py-1 text-xs font-semibold text-brand-700">
                Featured
            </span>
        @endif
        @if ($canBook)
            <button
                type="button"
                data-wishlist-toggle
                data-url="{{ route('account.wishlist.toggle', $property) }}"
                data-wishlisted="{{ $isWishlisted ? '1' : '0' }}"
                onclick="event.preventDefault(); window.toggleWishlist(this);"
                class="absolute top-3 right-3 z-20 h-8 w-8 flex items-center justify-center rounded-full bg-white/90 {{ $isWishlisted ? 'text-red-500' : 'text-gray-400' }} shadow"
                title="{{ $isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}"
            >
                {{ $isWishlisted ? '♥' : '♡' }}
            </button>
        @endif
    </div>

    <div class="p-4 flex-1 flex flex-col">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 truncate">{{ $property->name }}</h3>
                <p class="text-sm text-gray-500">{{ $property->city }}, {{ $property->country }}</p>

                @if (! $hideRating && $property->stars)
                    <div class="mt-1 flex items-center gap-0.5">
                        @for ($i = 0; $i < $property->stars; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.958c.299.921-.756 1.688-1.54 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.784.57-1.838-.197-1.539-1.118l1.286-3.958a1 1 0 00-.363-1.118L2.98 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" />
                            </svg>
                        @endfor
                    </div>
                @endif
            </div>

            @unless ($hideRating)
                <div class="shrink-0 flex flex-col items-end gap-1">
                    <span class="inline-flex items-center justify-center rounded-md {{ $ratingColor }} text-white text-sm font-semibold h-8 w-8">
                        {{ number_format($property->rating, 1) }}
                    </span>
                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $property->rating_label }}</span>
                </div>
            @endunless
        </div>

        @unless ($hideRating)
            <p class="mt-1 text-xs text-gray-400">{{ number_format($property->review_count) }} reviews</p>
        @endunless

        <div class="mt-3 flex flex-nowrap items-center gap-1.5 overflow-hidden">
            @foreach (array_slice($property->amenities, 0, 3) as $amenity)
                <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-xs text-brand-700 whitespace-nowrap min-w-0 truncate">{{ $amenity }}</span>
            @endforeach
        </div>

        <div class="mt-auto pt-4 flex items-end justify-between gap-2">
            <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $property->property_type) }} &middot; up to {{ $property->max_guests }} guests</span>
            <div class="text-right shrink-0">
                <div class="text-lg font-bold text-gray-900">${{ number_format($property->price_per_night, 0) }}</div>
                <div class="text-xs text-gray-500">per night</div>
            </div>
        </div>
    </div>
</div>
