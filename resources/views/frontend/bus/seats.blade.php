<x-site-layout :title="__('Select Seats').' — '.$route->from_city.' to '.$route->to_city.' | '.site('name')"
    :breadcrumbs="[[__('Bus Tickets'), route('bus.index')], [$route->from_city.' – '.$route->to_city, route('bus.search', ['from' => $route->from_city, 'to' => $route->to_city, 'date' => $date])], [__('Seats'), null]]">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{ selected: @js(old('seats', [])), booked: @js($booked), max: 10, target: {{ $passengers }}, price: {{ (float) $route->price }}, discount: 0,
            toggle(seat) {
                if (this.booked.includes(seat)) return;
                if (this.selected.includes(seat)) { this.selected = this.selected.filter(s => s !== seat); }
                else if (this.selected.length < this.max) { this.selected.push(seat); }
                this.discount = 0;
            },
            get subtotal() { return this.price * this.selected.length },
            fmt(v) { return 'Rs. ' + Math.round(v).toLocaleString('en-IN') } }">

        <a href="{{ route('bus.search', ['from' => $route->from_city, 'to' => $route->to_city, 'date' => $date, 'passengers' => $passengers]) }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-700"><x-icon name="chevron-left" class="h-4 w-4" /> {{ __('Back to results') }}</a>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8">
            <div class="space-y-6">
                <div class="rt-card flex flex-wrap items-center justify-between gap-4 p-5">
                    <div>
                        <h1 class="text-xl font-bold text-brand-950">{{ $route->bus_name }} <span class="text-sm font-medium text-slate-500">· {{ $route->operator }}</span></h1>
                        <p class="text-sm text-slate-500">{{ $route->from_city }} {{ $route->departure_label }} → {{ $route->to_city }} {{ $route->arrival_label }} · {{ $route->duration_label }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($date)->format('D, M d, Y') }}</p>
                        <p class="text-lg font-bold text-brand-700">{{ npr($route->price) }} <span class="text-xs font-normal text-slate-400">/ {{ __('seat') }}</span></p>
                    </div>
                </div>

                <div class="rt-card p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-semibold text-brand-950">{{ __('Choose your seats') }}</h2>
                        <div class="flex items-center gap-4 text-xs text-slate-500">
                            <span class="flex items-center gap-1.5"><span class="h-4 w-4 rounded border-2 border-slate-300 bg-white"></span>{{ __('Available') }}</span>
                            <span class="flex items-center gap-1.5"><span class="h-4 w-4 rounded bg-brand-700"></span>{{ __('Selected') }}</span>
                            <span class="flex items-center gap-1.5"><span class="h-4 w-4 rounded bg-slate-300"></span>{{ __('Booked') }}</span>
                        </div>
                    </div>

                    <div class="mx-auto mt-6 w-fit rounded-[2rem] border-4 border-slate-200 bg-slate-50 p-5">
                        <div class="mb-4 flex items-center justify-between border-b-2 border-dashed border-slate-200 pb-3 text-xs text-slate-400">
                            <span>{{ __('Door') }}</span>
                            <span class="flex items-center gap-1"><x-icon name="cog" class="h-6 w-6" /> {{ __('Driver') }}</span>
                        </div>
                        <div class="space-y-2">
                            @foreach ($route->seatRows() as $row)
                                <div class="flex gap-2">
                                    @foreach ($row as $seat)
                                        @if ($seat === null)
                                            <span class="w-6"></span>
                                        @else
                                            <button type="button" @click="toggle('{{ $seat }}')" :disabled="booked.includes('{{ $seat }}')"
                                                :class="booked.includes('{{ $seat }}') ? 'bg-slate-300 text-slate-500 cursor-not-allowed border-slate-300' : (selected.includes('{{ $seat }}') ? 'bg-brand-700 text-white border-brand-700 shadow-md' : 'bg-white text-slate-600 border-slate-300 hover:border-brand-500')"
                                                class="relative h-11 w-11 rounded-t-lg rounded-b-md border-2 text-[11px] font-semibold transition" :aria-pressed="selected.includes('{{ $seat }}')" aria-label="{{ __('Seat') }} {{ $seat }}">
                                                {{ $seat }}
                                                <span class="absolute inset-x-1.5 bottom-0.5 h-1 rounded-full bg-current opacity-20"></span>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <p class="mt-4 text-center text-sm text-slate-500" x-show="selected.length < target">{{ __('Select') }} <span x-text="target - selected.length"></span> {{ __('more seat(s) for your group.') }}</p>
                    <x-input-error for="seats" class="mt-2 text-center" />
                </div>

                <div class="rt-card grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 text-sm">
                    <div><p class="text-xs uppercase tracking-wide text-slate-400">{{ __('Boarding point') }}</p><p class="font-medium text-brand-950">{{ $route->boarding_point }} · {{ $route->departure_label }}</p></div>
                    <div><p class="text-xs uppercase tracking-wide text-slate-400">{{ __('Dropping point') }}</p><p class="font-medium text-brand-950">{{ $route->dropping_point }} · {{ $route->arrival_label }}</p></div>
                    <div class="sm:col-span-2 flex flex-wrap gap-1.5">
                        @foreach ($route->amenities ?? [] as $amenity)
                            <span class="rt-chip">{{ $amenity }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('bus.book', $route) }}" class="rt-card h-fit space-y-5 p-6 lg:sticky lg:top-28">
                @csrf
                <input type="hidden" name="travel_date" value="{{ $date }}">
                <template x-for="seat in selected" :key="seat"><input type="hidden" name="seats[]" :value="seat"></template>

                <div>
                    <h2 class="font-semibold text-brand-950">{{ __('Booking summary') }}</h2>
                    <div class="mt-3 flex flex-wrap gap-1.5 min-h-[28px]">
                        <template x-for="seat in selected" :key="seat"><span class="rounded-md bg-brand-700 px-2 py-1 text-xs font-bold text-white" x-text="seat"></span></template>
                        <span x-show="!selected.length" class="text-sm text-slate-400">{{ __('No seats selected yet') }}</span>
                    </div>
                </div>

                <x-frontend.traveler-fields service="bus" amount-expression="subtotal" />

                <dl class="space-y-1.5 border-t border-slate-100 pt-4 text-sm">
                    <div class="flex justify-between text-slate-600"><dt><span x-text="selected.length"></span> × {{ npr($route->price) }}</dt><dd x-text="fmt(subtotal)"></dd></div>
                    <div class="flex justify-between text-emerald-600" x-show="discount > 0" x-cloak><dt>{{ __('Coupon discount') }}</dt><dd x-text="'- ' + fmt(discount)"></dd></div>
                    <div class="flex justify-between pt-2 text-base font-bold text-brand-950"><dt>{{ __('Total') }}</dt><dd x-text="fmt(Math.max(0, subtotal - discount))"></dd></div>
                </dl>

                <button :disabled="!selected.length" class="rt-btn-gold w-full">{{ __('Book Seats') }} <x-icon name="arrow-right" class="h-4 w-4" /></button>
                <p class="text-center text-xs text-slate-400">{{ __('Seats are held for you once booked. Pay online to receive your e-ticket.') }}</p>
            </form>
        </div>
    </div>
</x-site-layout>
