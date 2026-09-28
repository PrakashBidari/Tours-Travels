<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Visa Services</h2>
            <a href="{{ route('dashboard.visa-services.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Visa Service</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No visa services yet." class="min-w-full">
                        <thead>
                            <tr>
                                <th>Visa</th>
                                <th data-filter data-filter-label="Type">Type</th>
                                <th>Processing</th>
                                <th>Total fee</th>
                                <th>Applications</th>
                                <th data-filter data-filter-label="Status">Status</th>
                                <th data-no-sort class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($visas as $visa)
                                <tr>
                                    <td class="font-medium text-gray-900 dark:text-gray-100"><span class="mr-1 text-lg">{{ $visa->flag }}</span> {{ $visa->title }}</td>
                                    <td>{{ $visa->type_label }}</td>
                                    <td>{{ $visa->processing_time }}</td>
                                    <td data-order="{{ $visa->total_fee }}">{{ npr($visa->total_fee) }}</td>
                                    <td><a href="{{ route('dashboard.service-bookings.index', ['service' => 'visa', 'q' => $visa->title]) }}" class="text-indigo-600">{{ $visa->bookings_count }}</a></td>
                                    <td><x-admin.status-badge :status="$visa->is_active ? 'active' : 'hidden'" /></td>
                                    <td class="text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('dashboard.visa-services.edit', $visa) }}" class="text-xs font-medium text-indigo-600">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.visa-services.destroy', $visa) }}" class="inline" data-confirm="Delete &quot;{{ $visa->title }}&quot;?">
                                            @csrf @method('DELETE')
                                            <button class="text-xs font-medium text-red-600">Delete</button>
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
