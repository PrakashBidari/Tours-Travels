<x-site-layout :title="__('Car Rental in Kathmandu — SUV, Jeep, Hiace, EV & Luxury').' | '.site('name')"
    :description="__('Rent cars, jeeps, Hiace vans, EVs and luxury wedding cars in Nepal — self drive or with driver. Airport pickup, tours and corporate rental.')"
    :image="config('travel.images.car')" :breadcrumbs="[[__('Car Rental'), null]]">

    <x-frontend.page-hero :title="__('Car Rental')" :eyebrow="__('Choose your perfect ride')" :image="config('travel.images.car')"
        :subtitle="__('SUVs, jeeps, Hiace vans, EVs and luxury cars — self drive or with an experienced driver.')" :breadcrumbs="[[__('Car Rental'), null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <form method="GET" action="{{ route('cars.index') }}" class="rt-card grid grid-cols-2 lg:grid-cols-[1.2fr_1fr_1fr_1fr_1fr_auto] items-end gap-3 p-4 sm:p-5">
            <div class="col-span-2 lg:col-span-1">
                <label for="car-pickup" class="rt-label">{{ __('Pickup location') }}</label>
                <input id="car-pickup" name="pickup" value="{{ $search['pickup'] ?? '' }}" placeholder="{{ __('e.g. Tribhuvan Airport') }}" class="rt-input">
            </div>
            <div>
                <label for="car-from" class="rt-label">{{ __('Pickup date') }}</label>
                <input id="car-from" type="date" name="from" value="{{ $search['from'] ?? '' }}" min="{{ now()->toDateString() }}" class="rt-input">
            </div>
            <div>
                <label for="car-to" class="rt-label">{{ __('Return date') }}</label>
                <input id="car-to" type="date" name="to" value="{{ $search['to'] ?? '' }}" min="{{ now()->toDateString() }}" class="rt-input">
            </div>
            <div>
                <label for="car-category" class="rt-label">{{ __('Vehicle type') }}</label>
                <select id="car-category" name="category" class="rt-input">
                    <option value="">{{ __('All vehicles') }}</option>
                    @foreach (config('travel.vehicle_categories') as $key => $label)
                        <option value="{{ $key }}" @selected(($search['category'] ?? '') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="car-mode" class="rt-label">{{ __('Drive option') }}</label>
                <select id="car-mode" name="mode" class="rt-input">
                    <option value="">{{ __('With driver or self') }}</option>
                    <option value="self" @selected(($search['mode'] ?? '') === 'self')>{{ __('Self drive only') }}</option>
                </select>
            </div>
            <button class="rt-btn-primary col-span-2 lg:col-span-1 h-[42px] py-0"><x-icon name="search" class="h-4 w-4" /> {{ __('Search') }}</button>
        </form>

        <div class="mt-6 flex flex-wrap gap-2">
            @foreach (\App\Http\Controllers\Frontend\CarController::SERVICES as $service)
                <a href="{{ route('cars.index', array_merge($search, ['service' => ($search['service'] ?? null) === $service ? null : $service])) }}"
                    class="rounded-full px-4 py-2 text-sm font-medium transition {{ ($search['service'] ?? null) === $service ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-700' }}">{{ __($service) }}</a>
            @endforeach
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($vehicles as $vehicle)
                <article class="rt-card group flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-glow">
                    <a href="{{ route('cars.show', array_merge(['vehicle' => $vehicle], array_filter($search))) }}" class="relative block aspect-[16/10] overflow-hidden">
                        <img src="{{ $vehicle->main_image }}" alt="{{ $vehicle->name }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <span class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[11px] font-semibold text-brand-700">{{ $vehicle->category_label }}</span>
                        @if ($vehicle->self_drive)<span class="absolute right-3 top-3 rounded-full bg-emerald-500 px-2.5 py-1 text-[11px] font-semibold text-white">{{ __('Self drive') }}</span>@endif
                    </a>
                    <div class="flex flex-1 flex-col p-5">
                        <h2 class="font-semibold text-brand-950"><a href="{{ route('cars.show', $vehicle) }}" class="hover:text-brand-700">{{ $vehicle->name }}</a></h2>
                        <div class="mt-3 grid grid-cols-4 gap-2 text-center text-[11px] text-slate-500">
                            <span class="rounded-lg bg-slate-50 py-2"><x-icon name="users" class="mx-auto h-4 w-4 text-sky-500" />{{ $vehicle->seats }} {{ __('seats') }}</span>
                            <span class="rounded-lg bg-slate-50 py-2"><x-icon name="briefcase" class="mx-auto h-4 w-4 text-sky-500" />{{ $vehicle->luggage }} {{ __('bags') }}</span>
                            <span class="rounded-lg bg-slate-50 py-2"><x-icon name="cog" class="mx-auto h-4 w-4 text-sky-500" />{{ __($vehicle->transmission) }}</span>
                            <span class="rounded-lg bg-slate-50 py-2"><x-icon name="fuel" class="mx-auto h-4 w-4 text-sky-500" />{{ __($vehicle->fuel) }}</span>
                        </div>
                        <div class="mt-auto flex items-end justify-between pt-5">
                            <div>
                                <p class="text-[11px] text-slate-400">{{ __('From') }}</p>
                                <p class="text-lg font-bold text-brand-700">{{ money($vehicle->price_per_day) }} <span class="text-xs font-normal text-slate-400">/ {{ __('day') }}</span></p>
                                @if ($vehicle->driver_charge_per_day > 0)<p class="text-[11px] text-slate-400">+ {{ money($vehicle->driver_charge_per_day) }} {{ __('driver/day') }}</p>@endif
                            </div>
                            <a href="{{ route('cars.show', array_merge(['vehicle' => $vehicle], array_filter($search))) }}#book" class="rt-btn-primary px-4 py-2 text-xs">{{ __('Book Vehicle') }}</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rt-card col-span-full p-12 text-center">
                    <x-icon name="car" class="mx-auto h-10 w-10 text-slate-300" />
                    <h2 class="mt-4 font-semibold text-brand-950">{{ __('No vehicles match your search') }}</h2>
                    <a href="{{ route('cars.index') }}" class="rt-btn-primary mt-5">{{ __('Show all vehicles') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</x-site-layout>
