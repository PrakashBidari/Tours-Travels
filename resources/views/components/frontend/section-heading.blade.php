@props(['eyebrow' => null, 'title', 'subtitle' => null, 'link' => null, 'linkLabel' => null, 'center' => false, 'light' => false])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-3 '.($center ? 'items-center text-center' : 'sm:flex-row sm:items-end sm:justify-between')]) }}>
    <div class="{{ $center ? 'max-w-2xl' : '' }}">
        @if ($eyebrow)
            <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.15em] {{ $center ? 'justify-center' : '' }} {{ $light ? 'text-gold-300' : 'text-brand-700' }}">
                <span class="h-0.5 w-6 rounded bg-gold-500"></span>{{ $eyebrow }}
            </p>
        @endif
        <h2 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight {{ $light ? 'text-white' : 'text-brand-950' }}">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-2 {{ $light ? 'text-brand-100' : 'text-slate-500' }}">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($link)
        <a href="{{ $link }}" class="group inline-flex shrink-0 items-center gap-1.5 text-sm font-semibold {{ $light ? 'text-gold-300' : 'text-brand-700 hover:text-brand-800' }}">
            {{ $linkLabel ?? __('View all') }}
            <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" />
        </a>
    @endif
</div>
