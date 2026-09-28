<x-site-layout :title="'Search results - ' . config('app.name', 'Booking')">

    <form method="GET" action="{{ route('search') }}" data-search-form>
        <input type="hidden" name="view" value="{{ $view }}">

        <!-- Compact search bar -->
        <div class="bg-brand-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="bg-white rounded-xl shadow p-3">
                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                        <div class="sm:col-span-2 text-left">
                            <label for="destination" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Destination</label>
                            <input type="text" id="destination" name="destination" value="{{ request('destination') }}" placeholder="City, region or property"
                                class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <div class="text-left">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Dates</label>
                            <x-frontend.date-range-picker :checkin="request('checkin')" :checkout="request('checkout')" />
                        </div>
                        <div class="text-left">
                            <label for="guests" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Guests</label>
                            <input type="number" id="guests" name="guests" min="1" max="16" value="{{ request('guests', 2) }}" class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition h-[42px]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- Filters -->
                <aside class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-5 space-y-6 lg:sticky lg:top-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Filter by</h3>
                            <a href="{{ route('search') }}" data-no-ajax class="text-xs font-medium text-brand-700 hover:text-brand-800">Clear all</a>
                        </div>

                        <!-- Price range -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Price per night</h4>
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" min="0"
                                    class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <span class="text-gray-400">&mdash;</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0"
                                    class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>
                        </div>

                        <!-- Property type -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Property type</h4>
                            <div class="space-y-2">
                                @foreach ($propertyTypes as $value => $label)
                                    <label class="flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" name="type[]" value="{{ $value }}" {{ in_array($value, (array) request('type', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Star rating -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Star rating</h4>
                            <div class="space-y-2">
                                @foreach ([5, 4, 3, 2] as $star)
                                    <label class="flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" name="stars[]" value="{{ $star }}" {{ in_array($star, (array) request('stars', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                        <span class="flex items-center gap-0.5">
                                            @for ($i = 0; $i < $star; $i++)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.363 1.118l1.287 3.958c.299.921-.756 1.688-1.54 1.118l-3.367-2.447a1 1 0 00-1.176 0l-3.367 2.447c-.784.57-1.838-.197-1.539-1.118l1.286-3.958a1 1 0 00-.363-1.118L2.98 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.958z" /></svg>
                                            @endfor
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Review score -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Review score</h4>
                            <div class="space-y-2">
                                @foreach (['' => 'Any', '9' => '9+ Exceptional', '8' => '8+ Excellent', '7' => '7+ Very good', '6' => '6+ Good'] as $value => $label)
                                    <label class="flex items-center gap-2 text-sm text-gray-600">
                                        <input type="radio" name="rating_min" value="{{ $value }}" {{ request('rating_min', '') == $value ? 'checked' : '' }}
                                            class="border-gray-300 text-brand-600 focus:ring-brand-500">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Amenities -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Amenities</h4>
                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                @foreach ($amenityPool as $amenity)
                                    <label class="flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity }}" {{ in_array($amenity, (array) request('amenities', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                        {{ $amenity }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
                            Apply filters
                        </button>
                    </div>
                </aside>

                <!-- Results -->
                <div class="lg:col-span-3">
                    <div id="search-results">
                        @include('frontend.partials.search-results', ['properties' => $properties, 'view' => $view])
                    </div>
                </div>
            </div>
        </div>
    </form>

</x-site-layout>
