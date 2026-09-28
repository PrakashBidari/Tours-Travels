<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Bus Routes & Schedules</h2>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2 text-sm">
                    <label for="date" class="text-gray-500">Seats booked on</label>
                    <input id="date" type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                </form>
                <a href="{{ route('dashboard.bus-routes.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Route</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No routes yet." class="min-w-full">
                        <thead>
                            <tr>
                                <th data-filter data-filter-label="Route">Route</th>
                                <th>Bus</th>
                                <th data-filter data-filter-label="Operator">Operator</th>
                                <th>Departure</th>
                                <th>Fare</th>
                                <th>Booked {{ \Carbon\Carbon::parse($date)->format('M d') }}</th>
                                <th data-filter data-filter-label="Status">Status</th>
                                <th data-no-sort class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($routes as $route)
                                <tr>
                                    <td class="font-medium text-gray-900 dark:text-gray-100">{{ $route->from_city }} → {{ $route->to_city }}</td>
                                    <td>{{ $route->bus_name }}<div class="text-xs text-gray-500">{{ $route->type_label }} · {{ $route->total_seats }} seats</div></td>
                                    <td>{{ $route->operator }}</td>
                                    <td data-order="{{ $route->departure_time }}">{{ $route->departure_label }} → {{ $route->arrival_label }}</td>
                                    <td data-order="{{ $route->price }}">{{ npr($route->price) }}</td>
                                    <td data-order="{{ $route->booked_count }}">
                                        <a href="{{ route('dashboard.bus-routes.seats', [$route, 'date' => $date]) }}" class="font-medium text-indigo-600">{{ $route->booked_count }} / {{ $route->total_seats }}</a>
                                        <div class="mt-1 h-1.5 w-24 rounded-full bg-gray-100 dark:bg-gray-700"><div class="h-1.5 rounded-full bg-indigo-500" style="width: {{ round($route->booked_count / max(1, $route->total_seats) * 100) }}%"></div></div>
                                    </td>
                                    <td><x-admin.status-badge :status="$route->is_active ? 'active' : 'hidden'" /></td>
                                    <td class="text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('dashboard.bus-routes.seats', [$route, 'date' => $date]) }}" class="text-xs font-medium text-gray-600 dark:text-gray-300">Manifest</a>
                                        <a href="{{ route('dashboard.bus-routes.edit', $route) }}" class="text-xs font-medium text-indigo-600">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.bus-routes.destroy', $route) }}" class="inline" data-confirm="Delete this route and its seat reservations?">
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
