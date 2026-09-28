<x-site-layout :title="__('Rent :name in Kathmandu', ['name' => $vehicle->name]).' | '.site('name')" :description="$vehicle->description" :image="$vehicle->main_image"
    :breadcrumbs="[[__('Car Rental'), route('cars.index')], [$vehicle->name, null]]"
    :schema="[['@context' => 'https://schema.org', '@type' => 'Product', 'name' => $vehicle->name.' rental', 'image' => $vehicle->image_urls, 'description' => $vehicle->description, 'offers' => ['@type' => 'Offer', 'price' => (float) $vehicle->price_per_day, 'priceCurrency' => 'NPR', 'unitText' => 'DAY']]]">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="flex items-center gap-1.5 text-xs text-slate-500"><a href="{{ route('home') }}" class="hover:text-brand-700">{{ __('Home') }}</a><x-icon name="chevron-right" class="h-3.5 w-3.5" /><a href="{{ route('cars.index') }}" class="hover:text-brand-700">{{ __('Car Rental') }}</a><x-icon name="chevron-right" class="h-3.5 w-3.5" /><span class="text-slate-700">{{ $vehicle->name }}</span></nav>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8">
            <div class="space-y-6 min-w-0">
                <div class="overflow-hidden rounded-2xl">
                    @foreach ($vehicle->image_urls as $i => $url)
                        <a href="{{ $url }}" class="glightbox {{ $i > 0 ? 'hidden' : 'block' }}" data-gallery="vehicle">
                            <img src="{{ $url }}" alt="{{ $vehicle->name }}" class="aspect-[16/9] w-full object-cover">
                        </a>
                    @endforeach
                </div>

                <div>
                    <span class="rt-chip">{{ $vehicle->category_label }}</span>
                    <h1 class="mt-3 text-2xl sm:text-3xl font-bold text-brand-950">{{ $vehicle->name }}</h1>
                    <p class="mt-3 text-slate-600 leading-relaxed">{{ $vehicle->description }}</p>
                </div>

                <div class="rt-card grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 text-center">
                    @foreach ([['users', __('Seats'), $vehicle->seats], ['briefcase', __('Luggage'), $vehicle->luggage.' '.__('bags')], ['cog', __('Transmission'), __($vehicle->transmission)], ['fuel', __('Fuel'), __($vehicle->fuel)]] as [$icon, $label, $value])
                        <div><x-icon :name="$icon" class="mx-auto h-6 w-6 text-sky-500" /><p class="mt-1 text-[11px] uppercase text-slate-400">{{ $label }}</p><p class="font-semibold text-brand-950">{{ $value }}</p></div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="rt-card p-5">
                        <h2 class="font-semibold text-brand-950">{{ __('Features') }}</h2>
                        <ul class="mt-3 space-y-2">@foreach ($vehicle->features ?? [] as $feature)<li class="flex gap-2 text-sm text-slate-600"><x-icon name="check" class="h-5 w-5 text-emerald-500" /> {{ $feature }}</li>@endforeach</ul>
                    </div>
                    <div class="rt-card p-5">
                        <h2 class="font-semibold text-brand-950">{{ __('Available for') }}</h2>
                        <ul class="mt-3 space-y-2">@foreach ($vehicle->services ?? [] as $service)<li class="flex gap-2 text-sm text-slate-600"><x-icon name="check-circle" class="h-5 w-5 text-sky-500" /> {{ __($service) }}</li>@endforeach</ul>
                    </div>
                </div>

                <div class="rt-card p-5">
                    <h2 class="font-semibold text-brand-950">{{ __('Pricing') }}</h2>
                    <table class="mt-3 w-full text-sm">
                        <tr class="border-b border-slate-100"><td class="py-2 text-slate-600">{{ __('Vehicle rent per day') }}</td><td class="py-2 text-right font-semibold">{{ npr($vehicle->price_per_day) }}</td></tr>
                        <tr class="border-b border-slate-100"><td class="py-2 text-slate-600">{{ __('Driver charge per day (incl. meals & lodging)') }}</td><td class="py-2 text-right font-semibold">{{ npr($vehicle->driver_charge_per_day) }}</td></tr>
                        <tr><td class="py-2 text-slate-600">{{ __('Fuel, tolls & parking') }}</td><td class="py-2 text-right text-slate-500">{{ __('Paid by customer') }}</td></tr>
                    </table>
                </div>
            </div>

            <aside id="book" class="scroll-mt-28">
                <form method="POST" action="{{ route('cars.book', $vehicle) }}" class="rt-card space-y-4 p-6 lg:sticky lg:top-28"
                    x-data="{ from: @js(old('pickup_date', $search['from'] ?? '')), to: @js(old('return_date', $search['to'] ?? '')), mode: @js(old('drive_mode', 'driver')), rate: {{ (float) $vehicle->price_per_day }}, driver: {{ (float) $vehicle->driver_charge_per_day }}, discount: 0,
                        get days() { if (!this.from || !this.to) return 1; const d = Math.round((new Date(this.to) - new Date(this.from)) / 86400000) + 1; return Math.max(1, d) },
                        get daily() { return this.rate + (this.mode === 'driver' ? this.driver : 0) },
                        get subtotal() { return this.daily * this.days },
                        fmt(v) { return 'Rs. ' + Math.round(v).toLocaleString('en-IN') } }">
                    @csrf
                    <div class="flex items-baseline justify-between">
                        <h2 class="text-lg font-bold text-brand-950">{{ __('Book this vehicle') }}</h2>
                        <p class="text-sm"><span class="text-xl font-extrabold text-brand-700">{{ npr($vehicle->price_per_day) }}</span>/{{ __('day') }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 rounded-xl bg-slate-100 p-1 text-sm font-semibold">
                        <label class="cursor-pointer"><input type="radio" name="drive_mode" value="driver" x-model="mode" class="peer sr-only"><span class="block rounded-lg py-2 text-center text-slate-500 peer-checked:bg-white peer-checked:text-brand-700 peer-checked:shadow">{{ __('With Driver') }}</span></label>
                        <label class="{{ $vehicle->self_drive ? 'cursor-pointer' : 'cursor-not-allowed opacity-40' }}"><input type="radio" name="drive_mode" value="self" x-model="mode" @disabled(! $vehicle->self_drive) class="peer sr-only"><span class="block rounded-lg py-2 text-center text-slate-500 peer-checked:bg-white peer-checked:text-brand-700 peer-checked:shadow">{{ __('Self Drive') }}</span></label>
                    </div>

                    <div>
                        <label for="pickup_location" class="rt-label">{{ __('Pickup location') }} <span class="text-rose-500">*</span></label>
                        <input id="pickup_location" name="pickup_location" value="{{ old('pickup_location', $search['pickup'] ?? '') }}" required class="rt-input" placeholder="{{ __('Hotel, address or airport') }}">
                    </div>
                    <div>
                        <label for="dropoff_location" class="rt-label">{{ __('Drop-off location') }}</label>
                        <input id="dropoff_location" name="dropoff_location" value="{{ old('dropoff_location') }}" class="rt-input" placeholder="{{ __('Same as pickup') }}">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label for="pickup_date" class="rt-label">{{ __('Pickup date') }}</label><input id="pickup_date" type="date" name="pickup_date" x-model="from" min="{{ now()->toDateString() }}" required class="rt-input"><x-input-error for="pickup_date" class="mt-1" /></div>
                        <div><label for="return_date" class="rt-label">{{ __('Return date') }}</label><input id="return_date" type="date" name="return_date" x-model="to" :min="from || @js(now()->toDateString())" required class="rt-input"></div>
                        <div><label for="pickup_time" class="rt-label">{{ __('Pickup time') }}</label><input id="pickup_time" type="time" name="pickup_time" value="{{ old('pickup_time', '08:00') }}" required class="rt-input"></div>
                        <div><label for="passengers" class="rt-label">{{ __('Passengers') }}</label><input id="passengers" type="number" name="passengers" min="1" max="{{ $vehicle->seats }}" value="{{ old('passengers', 2) }}" required class="rt-input"></div>
                        <div class="col-span-2">
                            <label for="service" class="rt-label">{{ __('Purpose') }}</label>
                            <select id="service" name="service" class="rt-input">
                                <option value="">{{ __('General / tour') }}</option>
                                @foreach (\App\Http\Controllers\Frontend\CarController::SERVICES as $service)
                                    <option value="{{ $service }}" @selected(old('service') === $service)>{{ __($service) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2" x-show="mode === 'self'" x-cloak>
                            <label for="license_number" class="rt-label">{{ __('Driving licence number') }} <span class="text-rose-500">*</span></label>
                            <input id="license_number" name="license_number" value="{{ old('license_number') }}" :required="mode === 'self'" class="rt-input">
                            <x-input-error for="license_number" class="mt-1" />
                        </div>
                    </div>

                    <x-frontend.traveler-fields service="car" amount-expression="subtotal" />

                    <dl class="space-y-1.5 border-t border-slate-100 pt-4 text-sm">
                        <div class="flex justify-between text-slate-600"><dt x-text="fmt(daily) + ' × ' + days + ' ' + (days === 1 ? @js(__('day')) : @js(__('days')))"></dt><dd x-text="fmt(subtotal)"></dd></div>
                        <div class="flex justify-between text-emerald-600" x-show="discount > 0" x-cloak><dt>{{ __('Coupon discount') }}</dt><dd x-text="'- ' + fmt(discount)"></dd></div>
                        <div class="flex justify-between pt-2 text-base font-bold text-brand-950"><dt>{{ __('Estimated total') }}</dt><dd x-text="fmt(Math.max(0, subtotal - discount))"></dd></div>
                    </dl>
                    <button class="rt-btn-gold w-full">{{ __('Book Vehicle') }} <x-icon name="arrow-right" class="h-4 w-4" /></button>
                </form>
            </aside>
        </div>

        @if ($related->isNotEmpty())
            <section class="mt-14">
                <x-frontend.section-heading :title="__('Similar vehicles')" :link="route('cars.index')" />
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach ($related as $item)
                        <a href="{{ route('cars.show', $item) }}" class="rt-card group overflow-hidden">
                            <img src="{{ $item->main_image }}" alt="{{ $item->name }}" loading="lazy" class="aspect-[16/10] w-full object-cover transition group-hover:scale-105">
                            <div class="flex items-center justify-between p-4"><span class="font-semibold text-brand-950">{{ $item->name }}</span><span class="text-sm font-bold text-brand-700">{{ money($item->price_per_day) }}</span></div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-site-layout>
