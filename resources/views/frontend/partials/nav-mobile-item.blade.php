@foreach ($items as $item)
    @if ($item->children->isNotEmpty())
        <div x-data="{ open: false }">
            <button
                type="button"
                @click="open = !open"
                style="padding-left: {{ 12 + $depth * 16 }}px"
                class="w-full flex items-center justify-between px-3 py-2 rounded-md text-white/90 hover:bg-white/10"
            >
                <span>{{ $item->label }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
            <div x-show="open" x-cloak x-transition>
                @include('frontend.partials.nav-mobile-item', ['items' => $item->children, 'depth' => $depth + 1])
            </div>
        </div>
    @else
        <a
            href="{{ $item->url }}"
            target="{{ $item->target }}"
            @if ($item->target === '_blank') rel="noopener" @endif
            style="padding-left: {{ 12 + $depth * 16 }}px"
            class="block px-3 py-2 rounded-md text-white/90 hover:bg-white/10"
        >{{ $item->label }}</a>
    @endif
@endforeach
