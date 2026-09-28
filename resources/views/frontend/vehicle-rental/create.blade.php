<x-site-layout :title="'Rent a Vehicle - ' . config('app.name', 'Booking')">

    <!-- Hero -->
    <section class="bg-gradient-to-b from-brand-900 to-brand-700">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-20 pb-14 sm:pb-16 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight">Rent a Vehicle</h1>
            <p class="mt-4 text-lg text-brand-50 max-w-2xl mx-auto">Need a taxi, bike, or something else to get around? Tell us your pickup details and nearby rental partners will reach out.</p>
        </div>
    </section>

    <!-- Form -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 sm:-mt-12 relative z-10 pb-16">
        <div class="max-w-2xl mx-auto rounded-2xl bg-white shadow-md ring-1 ring-gray-900/5 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-gray-900">Request a ride</h2>
            <p class="mt-1 text-sm text-gray-500">Fill in your pickup and drop-off details below.</p>

            <x-validation-errors class="mt-4" />

            <form method="POST" action="{{ route('rent-vehicle.store') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <x-label for="vehicle_type" value="Vehicle Type" />
                    <select id="vehicle_type" name="vehicle_type" required class="border-gray-300 rounded-md shadow-sm block mt-1 w-full focus:border-brand-500 focus:ring-brand-500">
                        <option value="" disabled {{ old('vehicle_type') ? '' : 'selected' }}>Select a vehicle type</option>
                        <option value="taxi" @selected(old('vehicle_type') === 'taxi')>Taxi</option>
                        <option value="bike" @selected(old('vehicle_type') === 'bike')>Bike</option>
                        <option value="other" @selected(old('vehicle_type') === 'other')>Other</option>
                    </select>
                </div>

                <div>
                    <x-label for="pickup_location" value="Pickup Location" />
                    <x-input id="pickup_location" name="pickup_location" class="block mt-1 w-full" :value="old('pickup_location')" required />
                </div>

                <div>
                    <x-label for="dropoff_location" value="Drop-off Location" />
                    <x-input id="dropoff_location" name="dropoff_location" class="block mt-1 w-full" :value="old('dropoff_location')" required />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <x-label for="pickup_date" value="Pickup Date" />
                        <x-input id="pickup_date" name="pickup_date" type="date" min="{{ now()->toDateString() }}" class="block mt-1 w-full" :value="old('pickup_date')" required />
                    </div>

                    <div>
                        <x-label for="pickup_time" value="Pickup Time" />
                        <x-input id="pickup_time" name="pickup_time" type="time" class="block mt-1 w-full" :value="old('pickup_time')" required />
                    </div>
                </div>

                <p class="text-sm text-gray-500">You&rsquo;ll need to be signed in as a traveller to submit &mdash; you&rsquo;ll be asked to log in if you aren&rsquo;t already.</p>

                <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 transition">
                    Send request
                </button>
            </form>
        </div>
    </section>

</x-site-layout>
