<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Tour Packages</h2>
            <a href="{{ route('dashboard.tour-packages.create', request('category') ? ['category' => request('category')] : []) }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Package</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.tour-packages.index') }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ ! request('category') ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">All</a>
                @foreach (config('travel.tour_categories') as $key => $label)
                    <a href="{{ route('dashboard.tour-packages.index', ['category' => $key]) }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('category') === $key ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No packages yet." class="min-w-full">
                        <thead>
                            <tr>
                                <th data-no-sort>Image</th>
                                <th>Package</th>
                                <th data-filter data-filter-label="Category">Category</th>
                                <th>Price (NPR)</th>
                                <th>Duration</th>
                                <th>Bookings</th>
                                <th data-filter data-filter-label="Status">Status</th>
                                <th data-no-sort class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tours as $tour)
                                <tr>
                                    <td><img src="{{ $tour->main_image }}" class="h-10 w-14 rounded object-cover" alt=""></td>
                                    <td>
                                        <a href="{{ route('tours.show', $tour) }}" target="_blank" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600">{{ $tour->title }}</a>
                                        <div class="text-xs text-gray-500">{{ $tour->destination }}, {{ $tour->country }} @if ($tour->is_featured)<span class="ml-1 rounded bg-amber-100 px-1.5 text-amber-700">Featured</span>@endif</div>
                                    </td>
                                    <td>{{ $tour->category_label }}</td>
                                    <td data-order="{{ $tour->final_price }}">
                                        {{ number_format($tour->final_price) }}
                                        @if ($tour->discount_percent)<div class="text-xs text-gray-400 line-through">{{ number_format((float) $tour->price) }}</div>@endif
                                    </td>
                                    <td data-order="{{ $tour->duration_days }}">{{ $tour->duration_label }}</td>
                                    <td>{{ $tour->bookings_count }}</td>
                                    <td><x-admin.status-badge :status="$tour->is_active ? 'active' : 'hidden'" /></td>
                                    <td class="text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('dashboard.tour-packages.edit', $tour) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.tour-packages.destroy', $tour) }}" class="inline" data-confirm="Delete &quot;{{ $tour->title }}&quot;? Existing bookings are kept.">
                                            @csrf @method('DELETE')
                                            <button class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
