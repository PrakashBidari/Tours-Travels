{{-- Overrides Laravel's default pagination::tailwind view (see the vendor:publish rules in
     Laravel's ViewFinder — a resources/views/vendor/pagination/{view}.blade.php file always
     wins over the package's own view of the same name). Used by every `$paginator->links()`
     call in the app that doesn't pass an explicit view — currently the frontend Stays search
     results and the public Destinations page. --}}
@if ($paginator->hasPages())
    @php
        // Cap the numbered window to at most 5 pages on each side of the current page —
        // Laravel's default onEachSide+first/last-chunk algorithm can otherwise render a
        // long, cluttered run of numbers on large result sets.
        $onEachSide = 5;
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $windowStart = max($current - $onEachSide, 1);
        $windowEnd = min($current + $onEachSide, $last);
    @endphp
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center sm:justify-between gap-4">

        <p class="text-sm text-gray-500 order-2 sm:order-1">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-medium text-gray-700">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium text-gray-700">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-medium text-gray-700">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <div class="flex items-center gap-1.5 order-1 sm:order-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-gray-300 cursor-not-allowed">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 ring-1 ring-gray-200 bg-white hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200 transition">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Jump to first page, if it's outside the numbered window --}}
            @if ($windowStart > 1)
                <a href="{{ $paginator->url(1) }}" aria-label="{{ __('Go to page :page', ['page' => 1]) }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium text-gray-600 ring-1 ring-gray-200 bg-white hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200 transition">
                    1
                </a>
                @if ($windowStart > 2)
                    <span aria-disabled="true" class="flex h-9 w-9 items-center justify-center text-sm text-gray-400">...</span>
                @endif
            @endif

            {{-- Numbered window: up to 5 pages either side of the current page --}}
            @for ($page = $windowStart; $page <= $windowEnd; $page++)
                @if ($page == $current)
                    <span aria-current="page"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-600 text-sm font-semibold text-white shadow-sm">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium text-gray-600 ring-1 ring-gray-200 bg-white hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200 transition">
                        {{ $page }}
                    </a>
                @endif
            @endfor

            {{-- Jump to last page, if it's outside the numbered window --}}
            @if ($windowEnd < $last)
                @if ($windowEnd < $last - 1)
                    <span aria-disabled="true" class="flex h-9 w-9 items-center justify-center text-sm text-gray-400">...</span>
                @endif
                <a href="{{ $paginator->url($last) }}" aria-label="{{ __('Go to page :page', ['page' => $last]) }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-medium text-gray-600 ring-1 ring-gray-200 bg-white hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200 transition">
                    {{ $last }}
                </a>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-gray-500 ring-1 ring-gray-200 bg-white hover:bg-brand-50 hover:text-brand-700 hover:ring-brand-200 transition">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                    class="flex h-9 w-9 items-center justify-center rounded-full text-gray-300 cursor-not-allowed">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
