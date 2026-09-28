@props(['tour'])

<article {{ $attributes->merge(['class' => 'group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-slate-900/5 transition hover:-translate-y-1 hover:shadow-glow']) }}>
    <a href="{{ route('tours.show', $tour) }}" class="relative block aspect-[4/3] overflow-hidden">
        <img src="{{ $tour->main_image }}" alt="{{ $tour->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
        <div class="absolute inset-0 bg-gradient-to-t from-brand-950/60 via-transparent to-transparent"></div>
        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-brand-700 backdrop-blur">{{ $tour->category_label }}</span>
        @if ($tour->discount_percent)
            <span class="absolute right-3 top-3 rounded-full bg-gold-500 px-2.5 py-1 text-[11px] font-bold text-brand-950">-{{ $tour->discount_percent }}%</span>
        @endif
        <span class="absolute bottom-3 left-3 flex items-center gap-1 text-xs font-medium text-white">
            <x-icon name="clock" class="h-4 w-4" /> {{ $tour->duration_label }}
        </span>
    </a>

    <button type="button" data-wishlist-tour="{{ $tour->slug }}" data-title="{{ $tour->title }}" class="absolute right-3 top-12 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-slate-500 shadow hover:text-rose-500 transition" aria-label="{{ __('Save to wishlist') }}">
        <x-icon name="heart" class="h-4 w-4" />
    </button>

    <div class="flex flex-1 flex-col p-4">
        <p class="flex items-center gap-1 text-xs text-slate-500">
            <x-icon name="map-pin" class="h-3.5 w-3.5 text-sky-500" /> {{ $tour->destination }}{{ $tour->country !== $tour->destination ? ', '.$tour->country : '' }}
        </p>
        <h3 class="mt-1.5 font-semibold leading-snug text-brand-950 line-clamp-2">
            <a href="{{ route('tours.show', $tour) }}" class="hover:text-brand-700">{{ $tour->title }}</a>
        </h3>
        <div class="mt-2 flex items-center gap-1 text-xs">
            <x-icon name="star" :solid="true" class="h-4 w-4 text-gold-500" />
            <span class="font-semibold text-slate-700">{{ number_format((float) $tour->rating, 1) }}</span>
            <span class="text-slate-400">({{ $tour->review_count }} {{ __('reviews') }})</span>
        </div>
        <div class="mt-auto flex items-end justify-between pt-4">
            <div class="min-w-0">
                <p class="text-[11px] text-slate-400">
                    {{ __('From') }}
                    @if ($tour->discount_percent)
                        <span class="ml-1 line-through">{{ money($tour->price) }}</span>
                    @endif
                </p>
                <p class="text-lg font-bold text-brand-700">{{ money($tour->final_price) }}</p>
            </div>
            <a href="{{ route('tours.show', $tour) }}#book" class="shrink-0 whitespace-nowrap rounded-full bg-brand-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-brand-800">{{ __('Book Now') }}</a>
        </div>
    </div>
</article>
