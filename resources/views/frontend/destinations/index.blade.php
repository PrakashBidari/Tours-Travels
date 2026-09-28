<x-site-layout :title="'Destinations - ' . config('app.name', 'Booking')">

    <div class="bg-brand-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">Explore destinations</h1>
            <p class="mt-1 text-brand-50">Browse every destination on {{ config('app.name', 'Booking') }}.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form method="GET" action="{{ route('destinations.index') }}" class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-4 mb-8">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2 text-left">
                    <label for="q" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Search</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Destination, city or country"
                        class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                </div>

                <div class="text-left">
                    <label for="vendor_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Vendor</label>
                    <select id="vendor_id" name="vendor_id" class="w-full rounded-lg border-gray-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">All vendors</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->company_name ?: $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition h-[42px] w-full">
                        Search
                    </button>
                    @if (request('q') || request('vendor_id'))
                        <a href="{{ route('destinations.index') }}" class="text-sm text-gray-500 hover:text-gray-700 whitespace-nowrap">Clear</a>
                    @endif
                </div>
            </div>
        </form>

        <p class="text-gray-700 mb-5">
            <span class="font-semibold">{{ $destinations->total() }}</span> destination(s) found
        </p>

        @if ($destinations->isEmpty())
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-900/5 p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                <h3 class="mt-4 font-semibold text-gray-900">No destinations match your search</h3>
                <p class="mt-1 text-sm text-gray-500">Try a different search term or clear the filters.</p>
                <a href="{{ route('destinations.index') }}" class="mt-4 inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition">Clear all filters</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($destinations as $destination)
                    <a href="{{ route('search', ['destination_id' => $destination->id]) }}" class="group rounded-xl overflow-hidden shadow-sm ring-1 ring-gray-900/5 bg-white hover:shadow-md transition">
                        <div class="relative h-40">
                            <img src="{{ $destination->image_url }}" alt="{{ $destination->name }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                        </div>
                        <div class="p-4">
                            <div class="font-semibold text-gray-900">{{ $destination->name }}</div>
                            <div class="text-sm text-gray-500">{{ $destination->city ? $destination->city.', ' : '' }}{{ $destination->country }}</div>
                            <div class="mt-2 flex items-center justify-between text-xs text-gray-400">
                                <span>{{ $destination->properties_count }} package(s)</span>
                                <span>By {{ $destination->vendor?->company_name ?: $destination->vendor?->name ?? 'Admin' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $destinations->links() }}
            </div>
        @endif
    </div>

</x-site-layout>
