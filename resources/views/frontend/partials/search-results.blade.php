@php $view = $view === 'list' ? 'list' : 'grid'; @endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
    <p class="text-gray-700">
        <span class="font-semibold">{{ $properties->total() }}</span> properties found
        @if (request('destination'))
            in <span class="font-semibold">{{ request('destination') }}</span>
        @endif
    </p>

    <div class="flex items-center gap-3">
        <div class="inline-flex rounded-lg ring-1 ring-gray-200 overflow-hidden shrink-0">
            <button type="button" data-view-btn="grid" title="Grid view" aria-label="Grid view"
                class="view-toggle-btn h-9 w-9 flex items-center justify-center transition {{ $view === 'grid' ? 'bg-brand-600 text-white' : 'bg-white text-gray-400 hover:text-gray-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
            </button>
            <button type="button" data-view-btn="list" title="List view" aria-label="List view"
                class="view-toggle-btn h-9 w-9 flex items-center justify-center border-l border-gray-200 transition {{ $view === 'list' ? 'bg-brand-600 text-white' : 'bg-white text-gray-400 hover:text-gray-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <label for="sort" class="text-sm text-gray-500 hidden sm:inline">Sort by</label>
            <select id="sort" name="sort" class="rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="recommended" {{ request('sort', 'recommended') == 'recommended' ? 'selected' : '' }}>Top picks</option>
                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price (low to high)</option>
                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price (high to low)</option>
                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Top rated</option>
                <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Most reviewed</option>
            </select>
        </div>
    </div>
</div>

@if ($properties->isEmpty())
    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-12 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <h3 class="mt-4 font-semibold text-gray-900">No properties match your filters</h3>
        <p class="mt-1 text-sm text-gray-500">Try adjusting or clearing some filters.</p>
        <a href="{{ route('search') }}" data-no-ajax class="mt-4 inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition">Clear all filters</a>
    </div>
@else
    @if ($view === 'list')
        <div class="space-y-5">
            @foreach ($properties as $property)
                <x-frontend.property-card :property="$property" layout="list" />
            @endforeach
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($properties as $property)
                <x-frontend.property-card :property="$property" layout="grid" />
            @endforeach
        </div>
    @endif

    <div class="mt-8">
        {{ $properties->links() }}
    </div>
@endif
