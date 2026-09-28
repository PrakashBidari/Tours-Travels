<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Car Rental — Vehicles</h2>
            <a href="{{ route('dashboard.vehicles.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Vehicle</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No vehicles yet." class="min-w-full">
                        <thead>
                            <tr>
                                <th data-no-sort>Photo</th>
                                <th>Vehicle</th>
                                <th data-filter data-filter-label="Category">Category</th>
                                <th>Seats</th>
                                <th>Rate / day</th>
                                <th>Driver / day</th>
                                <th>Upcoming bookings</th>
                                <th data-filter data-filter-label="Status">Status</th>
                                <th data-no-sort class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vehicles as $vehicle)
                                <tr>
                                    <td><img src="{{ $vehicle->main_image }}" class="h-10 w-14 rounded object-cover" alt=""></td>
                                    <td class="font-medium text-gray-900 dark:text-gray-100">{{ $vehicle->name }}@if ($vehicle->self_drive)<div class="text-xs text-emerald-600">Self drive available</div>@endif</td>
                                    <td>{{ $vehicle->category_label }}</td>
                                    <td>{{ $vehicle->seats }}</td>
                                    <td data-order="{{ $vehicle->price_per_day }}">{{ npr($vehicle->price_per_day) }}</td>
                                    <td>{{ npr($vehicle->driver_charge_per_day) }}</td>
                                    <td>{{ $vehicle->bookings_count }}</td>
                                    <td><x-admin.status-badge :status="$vehicle->is_active ? 'active' : 'hidden'" /></td>
                                    <td class="text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('dashboard.vehicles.edit', $vehicle) }}" class="text-xs font-medium text-indigo-600">Edit / calendar</a>
                                        <form method="POST" action="{{ route('dashboard.vehicles.destroy', $vehicle) }}" class="inline" data-confirm="Delete &quot;{{ $vehicle->name }}&quot;?">
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
