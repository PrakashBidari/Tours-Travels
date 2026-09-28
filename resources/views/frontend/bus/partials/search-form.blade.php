@php
    $cities = config('travel.bus_cities');
    $from = $from ?? request('from', 'Kathmandu');
    $to = $to ?? request('to', 'Pokhara');
    $date = $date ?? request('date', now()->addDay()->toDateString());
    $passengers = $passengers ?? (int) request('passengers', 1);
@endphp
<form method="GET" action="{{ route('bus.search') }}" x-data="{ from: @js($from), to: @js($to) }" class="rt-card grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_auto_1fr_1fr_0.8fr_auto] items-end gap-3 p-4 sm:p-5">
    <div>
        <label for="bus-from" class="rt-label">{{ __('From') }}</label>
        <select id="bus-from" name="from" x-model="from" class="rt-input">@foreach ($cities as $city)<option value="{{ $city }}">{{ $city }}</option>@endforeach</select>
    </div>
    <button type="button" @click="[from, to] = [to, from]" class="hidden lg:flex mb-0.5 h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-700 hover:bg-brand-100" aria-label="{{ __('Swap cities') }}"><x-icon name="swap" class="h-5 w-5" /></button>
    <div>
        <label for="bus-to" class="rt-label">{{ __('To') }}</label>
        <select id="bus-to" name="to" x-model="to" class="rt-input">@foreach ($cities as $city)<option value="{{ $city }}">{{ $city }}</option>@endforeach</select>
    </div>
    <div>
        <label for="bus-date" class="rt-label">{{ __('Travel date') }}</label>
        <input id="bus-date" type="date" name="date" value="{{ $date }}" min="{{ now()->toDateString() }}" required class="rt-input">
    </div>
    <div>
        <label for="bus-pax" class="rt-label">{{ __('Passengers') }}</label>
        <select id="bus-pax" name="passengers" class="rt-input">@foreach (range(1, 10) as $n)<option value="{{ $n }}" @selected($passengers === $n)>{{ $n }}</option>@endforeach</select>
    </div>
    <button class="rt-btn-primary h-[42px] px-6 py-0"><x-icon name="search" class="h-4 w-4" /> {{ __('Search Buses') }}</button>
</form>
