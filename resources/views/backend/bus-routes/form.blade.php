<x-admin.form-page :title="$route->exists ? 'Edit route' : 'New Bus Route'" :back="route('dashboard.bus-routes.index')"
    :action="$route->exists ? route('dashboard.bus-routes.update', $route) : route('dashboard.bus-routes.store')" :method="$route->exists ? 'PUT' : 'POST'"
    :submit="$route->exists ? 'Save changes' : 'Add route'">

    <x-admin.card title="Route & bus">
        <x-admin.field name="from_city" label="From" :value="$route->from_city" required help="Match the city names used in search: {{ implode(', ', config('travel.bus_cities')) }}" />
        <x-admin.field name="to_city" label="To" :value="$route->to_city" required />
        <x-admin.field name="operator" label="Bus company / operator" :value="$route->operator" required />
        <x-admin.field name="bus_name" label="Bus name" :value="$route->bus_name" required />
        <x-admin.field name="bus_type" label="Bus type" type="select" :value="$route->bus_type" :options="\App\Models\BusRoute::TYPES" />
        <x-admin.field name="total_seats" label="Total seats" type="number" min="5" :value="$route->total_seats" required help="2+2 layout; 29, 30 and 35 seats end with a 5-seat back row." />
    </x-admin.card>

    <x-admin.card title="Schedule & fare">
        <x-admin.field name="departure_time" label="Departure time" type="time" :value="$route->departure_time ? substr($route->departure_time, 0, 5) : null" required />
        <x-admin.field name="arrival_time" label="Arrival time" type="time" :value="$route->arrival_time ? substr($route->arrival_time, 0, 5) : null" required />
        <x-admin.field name="boarding_point" label="Boarding point" :value="$route->boarding_point" />
        <x-admin.field name="dropping_point" label="Dropping point" :value="$route->dropping_point" />
        <x-admin.field name="price" label="Fare per seat (NPR)" type="number" step="0.01" min="0" :value="$route->price" required />
        <x-admin.field name="is_active" label="Bookable online" type="checkbox" :value="$route->is_active" />
        <x-admin.field name="amenities" label="Amenities" type="lines" :value="$route->amenities" />
        <x-admin.field name="image" label="Bus photo" type="image" :value="$route->image" full />
    </x-admin.card>
</x-admin.form-page>
