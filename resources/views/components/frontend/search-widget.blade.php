@props(['tab' => 'tour', 'tourDestinations' => collect(), 'visaCountries' => collect()])

@php
    $tabs = [
        'tour' => [__('Tour Package'), 'mountain'],
        'flight' => [__('Flight'), 'plane'],
        'bus' => [__('Bus'), 'bus'],
        'car' => [__('Car Rental'), 'car'],
        'hotel' => [__('Hotel'), 'hotel'],
        'visa' => [__('Visa'), 'visa'],
    ];
    $today = now()->toDateString();
    $field = 'mt-1.5 flex h-12 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100 transition';
    $input = 'w-full min-w-0 border-0 bg-transparent p-0 text-sm text-slate-700 placeholder:text-slate-400 focus:ring-0';
    $label = 'block text-[13px] font-semibold text-slate-800';
@endphp

<div x-data="{ tab: @js($tab) }" {{ $attributes }}>
    <!-- Tabs -->
    <div class="flex gap-1.5 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" role="tablist">
        @foreach ($tabs as $key => [$tabLabel, $icon])
            <button type="button" role="tab" @click="tab = '{{ $key }}'" :aria-selected="tab === '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'bg-brand-700 text-white shadow-lg shadow-brand-700/30' : 'bg-white text-slate-700 hover:text-brand-700'"
                class="flex shrink-0 items-center gap-2 rounded-t-xl rounded-b-none sm:rounded-xl sm:rounded-b-none px-4 sm:px-5 py-3 text-sm font-medium transition">
                <x-icon :name="$icon" class="h-[18px] w-[18px]" />
                {{ $tabLabel }}
            </button>
        @endforeach
    </div>

    <div class="-mt-px rounded-b-2xl rounded-tr-2xl bg-white p-4 sm:p-6 shadow-2xl shadow-brand-950/20 ring-1 ring-slate-900/5">

        <!-- Tour package -->
        <form x-show="tab === 'tour'" method="GET" action="{{ route('tours.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_1fr_auto] gap-4 items-end">
            <div>
                <label for="sw-tour-destination" class="{{ $label }}">{{ __('Destination') }}</label>
                <div class="{{ $field }}">
                    <x-icon name="map-pin" class="h-5 w-5 shrink-0 text-slate-400" />
                    <select id="sw-tour-destination" name="destination" class="{{ $input }} pr-6">
                        <option value="">{{ __('Where to?') }}</option>
                        @foreach ($tourDestinations as $destination)
                            <option value="{{ $destination }}">{{ $destination }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="sw-tour-in" class="{{ $label }}">{{ __('Check In') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-tour-in" type="date" name="date" min="{{ $today }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-tour-out" class="{{ $label }}">{{ __('Check Out') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-tour-out" type="date" name="return" min="{{ $today }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-tour-travelers" class="{{ $label }}">{{ __('Travelers') }}</label>
                <div class="{{ $field }}">
                    <x-icon name="users" class="h-5 w-5 shrink-0 text-slate-400" />
                    <select id="sw-tour-travelers" name="travelers" class="{{ $input }} pr-6">
                        @foreach (range(1, 15) as $n)
                            <option value="{{ $n }}" @selected($n === 2)>{{ $n }} {{ $n === 1 ? __('Adult') : __('Adults') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="flex h-12 items-center justify-center gap-2 rounded-lg bg-brand-700 px-8 text-sm font-semibold text-white shadow-lg shadow-brand-700/30 hover:bg-brand-800 transition sm:col-span-2 lg:col-span-1">
                <x-icon name="search" class="h-5 w-5" /> {{ __('Search') }}
            </button>
        </form>

        <!-- Flight -->
        <form x-show="tab === 'flight'" x-cloak x-data="{ trip: 'oneway' }" method="GET" action="{{ route('flights.index') }}" class="space-y-4">
            <div class="flex flex-wrap items-center gap-5 text-sm">
                @foreach (['oneway' => __('One Way'), 'round' => __('Round Trip'), 'multi' => __('Multi City')] as $value => $tripLabel)
                    <label class="flex cursor-pointer items-center gap-2 font-medium text-slate-700">
                        <input type="radio" name="trip_type" value="{{ $value }}" x-model="trip" class="text-brand-700 focus:ring-brand-500"> {{ $tripLabel }}
                    </label>
                @endforeach
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_1fr_1fr_auto] gap-4 items-end">
                @foreach (['from' => [__('From'), 'KTM'], 'to' => [__('To'), 'DXB']] as $name => [$fieldLabel, $default])
                    <div>
                        <label for="sw-flight-{{ $name }}" class="{{ $label }}">{{ $fieldLabel }}</label>
                        <div class="{{ $field }}">
                            <x-icon name="plane" class="h-5 w-5 shrink-0 text-slate-400 {{ $name === 'to' ? 'rotate-90' : '' }}" />
                            <select id="sw-flight-{{ $name }}" name="{{ $name }}" class="{{ $input }} pr-6">
                                @foreach (config('travel.airports') as $code => $airport)
                                    <option value="{{ $code }}" @selected($code === $default)>{{ $airport }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach
                <div>
                    <label for="sw-flight-depart" class="{{ $label }}">{{ __('Departure') }}</label>
                    <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-flight-depart" type="date" name="depart" min="{{ $today }}" class="{{ $input }}"></div>
                </div>
                <div :class="trip === 'round' ? '' : 'opacity-50'">
                    <label for="sw-flight-return" class="{{ $label }}">{{ __('Return') }}</label>
                    <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-flight-return" type="date" name="return" min="{{ $today }}" :disabled="trip !== 'round'" class="{{ $input }}"></div>
                </div>
                <div>
                    <label for="sw-flight-cabin" class="{{ $label }}">{{ __('Travelers & Cabin') }}</label>
                    <div class="{{ $field }}">
                        <x-icon name="users" class="h-5 w-5 shrink-0 text-slate-400" />
                        <select name="adults" aria-label="{{ __('Adults') }}" class="{{ $input }} w-auto pr-6">
                            @foreach (range(1, 9) as $n)<option value="{{ $n }}">{{ $n }}</option>@endforeach
                        </select>
                        <select id="sw-flight-cabin" name="cabin" class="{{ $input }} pr-6">
                            <option value="economy">{{ __('Economy') }}</option>
                            <option value="premium">{{ __('Premium') }}</option>
                            <option value="business">{{ __('Business') }}</option>
                        </select>
                    </div>
                </div>
                <button class="flex h-12 items-center justify-center gap-2 rounded-lg bg-brand-700 px-8 text-sm font-semibold text-white shadow-lg shadow-brand-700/30 hover:bg-brand-800 transition sm:col-span-2 lg:col-span-1">
                    <x-icon name="search" class="h-5 w-5" /> {{ __('Search') }}
                </button>
            </div>
        </form>

        <!-- Bus -->
        <form x-show="tab === 'bus'" x-cloak method="GET" action="{{ route('bus.search') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_1fr_auto] gap-4 items-end">
            @foreach (['from' => [__('From'), 'Kathmandu'], 'to' => [__('To'), 'Pokhara']] as $name => [$fieldLabel, $default])
                <div>
                    <label for="sw-bus-{{ $name }}" class="{{ $label }}">{{ $fieldLabel }}</label>
                    <div class="{{ $field }}">
                        <x-icon name="map-pin" class="h-5 w-5 shrink-0 text-slate-400" />
                        <select id="sw-bus-{{ $name }}" name="{{ $name }}" class="{{ $input }} pr-6">
                            @foreach (config('travel.bus_cities') as $city)
                                <option value="{{ $city }}" @selected($city === $default)>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endforeach
            <div>
                <label for="sw-bus-date" class="{{ $label }}">{{ __('Travel Date') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-bus-date" type="date" name="date" min="{{ $today }}" value="{{ now()->addDay()->toDateString() }}" required class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-bus-pax" class="{{ $label }}">{{ __('Passengers') }}</label>
                <div class="{{ $field }}">
                    <x-icon name="users" class="h-5 w-5 shrink-0 text-slate-400" />
                    <select id="sw-bus-pax" name="passengers" class="{{ $input }} pr-6">
                        @foreach (range(1, 10) as $n)<option value="{{ $n }}">{{ $n }} {{ $n === 1 ? __('Passenger') : __('Passengers') }}</option>@endforeach
                    </select>
                </div>
            </div>
            <button class="flex h-12 items-center justify-center gap-2 rounded-lg bg-brand-700 px-8 text-sm font-semibold text-white shadow-lg shadow-brand-700/30 hover:bg-brand-800 transition sm:col-span-2 lg:col-span-1">
                <x-icon name="search" class="h-5 w-5" /> {{ __('Search') }}
            </button>
        </form>

        <!-- Car rental -->
        <form x-show="tab === 'car'" x-cloak method="GET" action="{{ route('cars.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.2fr_1fr_1fr_1fr_auto] gap-4 items-end">
            <div>
                <label for="sw-car-pickup" class="{{ $label }}">{{ __('Pickup Location') }}</label>
                <div class="{{ $field }}"><x-icon name="map-pin" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-car-pickup" type="text" name="pickup" placeholder="{{ __('e.g. Tribhuvan Airport') }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-car-from" class="{{ $label }}">{{ __('Pickup Date') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-car-from" type="date" name="from" min="{{ $today }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-car-to" class="{{ $label }}">{{ __('Return Date') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-car-to" type="date" name="to" min="{{ $today }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-car-type" class="{{ $label }}">{{ __('Vehicle Type') }}</label>
                <div class="{{ $field }}">
                    <x-icon name="car" class="h-5 w-5 shrink-0 text-slate-400" />
                    <select id="sw-car-type" name="category" class="{{ $input }} pr-6">
                        <option value="">{{ __('Any vehicle') }}</option>
                        @foreach (config('travel.vehicle_categories') as $key => $categoryLabel)
                            <option value="{{ $key }}">{{ $categoryLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="flex h-12 items-center justify-center gap-2 rounded-lg bg-brand-700 px-8 text-sm font-semibold text-white shadow-lg shadow-brand-700/30 hover:bg-brand-800 transition sm:col-span-2 lg:col-span-1">
                <x-icon name="search" class="h-5 w-5" /> {{ __('Search') }}
            </button>
        </form>

        <!-- Hotel (existing property search) -->
        <form x-show="tab === 'hotel'" x-cloak method="GET" action="{{ route('search') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_1fr_auto] gap-4 items-end">
            <div>
                <label for="sw-hotel-dest" class="{{ $label }}">{{ __('City or Hotel') }}</label>
                <div class="{{ $field }}"><x-icon name="hotel" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-hotel-dest" type="text" name="destination" placeholder="{{ __('Where are you going?') }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-hotel-in" class="{{ $label }}">{{ __('Check In') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-hotel-in" type="date" name="checkin" min="{{ $today }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-hotel-out" class="{{ $label }}">{{ __('Check Out') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-hotel-out" type="date" name="checkout" min="{{ $today }}" class="{{ $input }}"></div>
            </div>
            <div>
                <label for="sw-hotel-guests" class="{{ $label }}">{{ __('Guests') }}</label>
                <div class="{{ $field }}">
                    <x-icon name="users" class="h-5 w-5 shrink-0 text-slate-400" />
                    <select id="sw-hotel-guests" name="guests" class="{{ $input }} pr-6">
                        @foreach (range(1, 10) as $n)<option value="{{ $n }}" @selected($n === 2)>{{ $n }} {{ $n === 1 ? __('Guest') : __('Guests') }}</option>@endforeach
                    </select>
                </div>
            </div>
            <button class="flex h-12 items-center justify-center gap-2 rounded-lg bg-brand-700 px-8 text-sm font-semibold text-white shadow-lg shadow-brand-700/30 hover:bg-brand-800 transition sm:col-span-2 lg:col-span-1">
                <x-icon name="search" class="h-5 w-5" /> {{ __('Search') }}
            </button>
        </form>

        <!-- Visa -->
        <form x-show="tab === 'visa'" x-cloak method="GET" action="{{ route('visa.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_auto] gap-4 items-end">
            <div>
                <label for="sw-visa-country" class="{{ $label }}">{{ __('Destination Country') }}</label>
                <div class="{{ $field }}">
                    <x-icon name="globe" class="h-5 w-5 shrink-0 text-slate-400" />
                    <select id="sw-visa-country" name="country" class="{{ $input }} pr-6">
                        <option value="">{{ __('All countries') }}</option>
                        @foreach ($visaCountries as $country)
                            <option value="{{ $country }}">{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="sw-visa-type" class="{{ $label }}">{{ __('Visa Type') }}</label>
                <div class="{{ $field }}">
                    <x-icon name="visa" class="h-5 w-5 shrink-0 text-slate-400" />
                    <select id="sw-visa-type" name="type" class="{{ $input }} pr-6">
                        <option value="">{{ __('Any type') }}</option>
                        @foreach (config('travel.visa_types') as $key => $typeLabel)
                            <option value="{{ $key }}">{{ __($typeLabel) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label for="sw-visa-date" class="{{ $label }}">{{ __('Travel Date') }}</label>
                <div class="{{ $field }}"><x-icon name="calendar" class="h-5 w-5 shrink-0 text-slate-400" /><input id="sw-visa-date" type="date" name="date" min="{{ $today }}" class="{{ $input }}"></div>
            </div>
            <button class="flex h-12 items-center justify-center gap-2 rounded-lg bg-brand-700 px-8 text-sm font-semibold text-white shadow-lg shadow-brand-700/30 hover:bg-brand-800 transition sm:col-span-2 lg:col-span-1">
                <x-icon name="search" class="h-5 w-5" /> {{ __('Search') }}
            </button>
        </form>
    </div>
</div>
