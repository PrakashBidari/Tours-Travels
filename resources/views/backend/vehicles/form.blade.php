<x-admin.form-page :title="$vehicle->exists ? 'Edit: '.$vehicle->name : 'New Vehicle'" :back="route('dashboard.vehicles.index')"
    :action="$vehicle->exists ? route('dashboard.vehicles.update', $vehicle) : route('dashboard.vehicles.store')" :method="$vehicle->exists ? 'PUT' : 'POST'"
    :submit="$vehicle->exists ? 'Save changes' : 'Add vehicle'">

    @if ($vehicle->exists)
        @php
            $days = collect(range(0, 41))->map(fn ($i) => today()->startOfWeek()->addDays($i));
            $bookedOn = fn ($day) => ($upcoming ?? collect())->first(fn ($b) => $day->between($b->travel_date, $b->return_date ?? $b->travel_date));
        @endphp
        <section class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Availability — next 6 weeks</h3>
            <div class="mt-4 grid grid-cols-7 gap-1 text-center text-xs">
                @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dow)<div class="font-medium text-gray-400">{{ $dow }}</div>@endforeach
                @foreach ($days as $day)
                    @php $booking = $bookedOn($day); @endphp
                    <div title="{{ $booking ? $booking->reference.' — '.$booking->full_name : 'Available' }}"
                        class="rounded-md py-2 {{ $day->isPast() && ! $day->isToday() ? 'opacity-40' : '' }} {{ $booking ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300 font-semibold' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' }} {{ $day->isToday() ? 'ring-2 ring-indigo-500' : '' }}">
                        {{ $day->format('M j') }}
                    </div>
                @endforeach
            </div>
            @if (($upcoming ?? collect())->isNotEmpty())
                <ul class="mt-4 divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                    @foreach ($upcoming as $booking)
                        <li class="flex items-center justify-between py-2">
                            <a href="{{ route('dashboard.service-bookings.show', $booking) }}" class="font-mono text-indigo-600">{{ $booking->reference }}</a>
                            <span class="text-gray-600 dark:text-gray-300">{{ $booking->full_name }}</span>
                            <span class="text-gray-500">{{ $booking->travel_date->format('M d') }} → {{ $booking->return_date?->format('M d') }}</span>
                            <x-admin.status-badge :status="$booking->status" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endif

    <x-admin.card title="Vehicle">
        <x-admin.field name="name" label="Vehicle name" :value="$vehicle->name" required full />
        <x-admin.field name="category" label="Category" type="select" :value="$vehicle->category" :options="config('travel.vehicle_categories')" required />
        <x-admin.field name="seats" label="Seats" type="number" min="1" :value="$vehicle->seats" required />
        <x-admin.field name="luggage" label="Luggage (bags)" type="number" min="0" :value="$vehicle->luggage" required />
        <x-admin.field name="transmission" label="Transmission" type="select" :value="$vehicle->transmission" :options="['Manual' => 'Manual', 'Automatic' => 'Automatic']" />
        <x-admin.field name="fuel" label="Fuel" type="select" :value="$vehicle->fuel" :options="['Diesel' => 'Diesel', 'Petrol' => 'Petrol', 'Electric' => 'Electric', 'Hybrid' => 'Hybrid']" />
        <x-admin.field name="description" label="Description" type="textarea" :value="$vehicle->description" />
    </x-admin.card>

    <x-admin.card title="Pricing & options">
        <x-admin.field name="price_per_day" label="Rent per day (NPR)" type="number" step="0.01" min="0" :value="$vehicle->price_per_day" required />
        <x-admin.field name="driver_charge_per_day" label="Driver charge per day (NPR)" type="number" step="0.01" min="0" :value="$vehicle->driver_charge_per_day ?? 0" required />
        <x-admin.field name="with_driver" label="Available with driver" type="checkbox" :value="$vehicle->with_driver" />
        <x-admin.field name="self_drive" label="Available as self drive" type="checkbox" :value="$vehicle->self_drive" />
        <x-admin.field name="is_active" label="Show on website" type="checkbox" :value="$vehicle->is_active" />
        <x-admin.field name="is_featured" label="Featured" type="checkbox" :value="$vehicle->is_featured" />
        <x-admin.field name="services" label="Services (Airport Pickup & Drop, Wedding Car, Corporate Rental, Tour & Sightseeing, Self Drive)" type="lines" :value="$vehicle->services" />
        <x-admin.field name="features" label="Features" type="lines" :value="$vehicle->features" />
    </x-admin.card>

    <x-admin.card title="Photos">
        <x-admin.field name="images" type="images" :value="$vehicle->images" />
    </x-admin.card>
</x-admin.form-page>
