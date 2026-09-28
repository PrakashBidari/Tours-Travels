@foreach ($items as $item)
    @if ($item->children->isNotEmpty())
        <div class="relative" x-data="{ subOpen: false }" @mouseenter="subOpen = true" @mouseleave="subOpen = false">
            <a
                href="{{ $item->url }}"
                target="{{ $item->target }}"
                @if ($item->target === '_blank') rel="noopener" @endif
                class="flex items-center justify-between gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
            >
                {{ $item->label }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>

            <div
                x-show="subOpen"
                x-cloak
                x-transition
                style="z-index: {{ 60 + $depth * 10 }}"
                class="absolute left-full top-0 min-w-[180px] rounded-lg bg-white py-2 shadow-lg ring-1 ring-gray-900/5"
            >
                @include('frontend.partials.nav-dropdown', ['items' => $item->children, 'depth' => $depth + 1])
            </div>
        </div>
    @else
        <a
            href="{{ $item->url }}"
            target="{{ $item->target }}"
            @if ($item->target === '_blank') rel="noopener" @endif
            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
        >{{ $item->label }}</a>
    @endif
@endforeach
