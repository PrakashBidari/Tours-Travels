@props(['title', 'subtitle' => null, 'image' => null, 'breadcrumbs' => [], 'eyebrow' => null])

{{-- Banner used at the top of every inner page, with breadcrumb navigation. --}}
<section class="relative overflow-hidden bg-brand-950">
    <img src="{{ $image ?? config('travel.images.mountains') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-60">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-950 via-brand-900/80 to-brand-700/30"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20">
        <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-1.5 text-xs font-medium text-brand-100">
            <a href="{{ route('home') }}" class="hover:text-gold-400">{{ __('Home') }}</a>
            @foreach ($breadcrumbs as $crumb)
                <x-icon name="chevron-right" class="h-3.5 w-3.5 text-brand-300" />
                @if (! $loop->last && ($crumb[1] ?? null))
                    <a href="{{ $crumb[1] }}" class="hover:text-gold-400">{{ $crumb[0] }}</a>
                @else
                    <span class="text-gold-400">{{ $crumb[0] }}</span>
                @endif
            @endforeach
        </nav>
        @if ($eyebrow)
            <p class="mt-5 font-script text-2xl text-gold-400">{{ $eyebrow }}</p>
        @endif
        <h1 class="{{ $eyebrow ? 'mt-1' : 'mt-5' }} max-w-3xl text-3xl sm:text-5xl font-bold tracking-tight text-white">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-4 max-w-2xl text-base sm:text-lg text-brand-100">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
