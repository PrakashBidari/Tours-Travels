<x-site-layout :title="__(':from to :to Bus Tickets', ['from' => $from, 'to' => $to]).' | '.site('name')"
    :description="__('Compare :from to :to bus timings, prices and seat availability. Book tourist, deluxe and sofa bus tickets online.', ['from' => $from, 'to' => $to])"
    :breadcrumbs="[[__('Bus Tickets'), route('bus.index')], [$from.' – '.$to, null]]">

    <section class="bg-brand-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16">
            <nav class="flex items-center gap-1.5 text-xs text-brand-200"><a href="{{ route('bus.index') }}" class="hover:text-gold-400">{{ __('Bus Tickets') }}</a><x-icon name="chevron-right" class="h-3.5 w-3.5" /><span class="text-gold-400">{{ $from }} – {{ $to }}</span></nav>
            <h1 class="mt-3 text-2xl sm:text-3xl font-bold text-white">{{ $from }} <span class="text-gold-400">→</span> {{ $to }}</h1>
            <p class="mt-1 text-brand-100">{{ \Carbon\Carbon::parse($date)->format('l, M d, Y') }} · {{ $passengers }} {{ Str::plural(__('passenger'), $passengers) }}</p>
        </div>
    </section>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
        @include('frontend.bus.partials.search-form')
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Date strip -->
        <div class="flex gap-2 overflow-x-auto pb-2">
            @foreach (range(0, 6) as $offset)
                @php $day = now()->addDays($offset); @endphp
                <a href="{{ route('bus.search', ['from' => $from, 'to' => $to, 'date' => $day->toDateString(), 'passengers' => $passengers]) }}"
                    class="shrink-0 rounded-xl px-4 py-2 text-center text-sm transition {{ $day->toDateString() === $date ? 'bg-brand-700 text-white shadow-md' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-brand-300' }}">
                    <span class="block text-xs opacity-75">{{ $day->format('D') }}</span><span class="font-semibold">{{ $day->format('M d') }}</span>
                </a>
            @endforeach
        </div>

        <p class="mt-6 text-sm text-slate-500"><span class="font-semibold text-brand-950">{{ $routes->count() }}</span> {{ Str::plural(__('bus'), $routes->count()) }} {{ __('found') }}</p>

        <div class="mt-4 space-y-4">
            @forelse ($routes as $route)
                <article class="rt-card grid grid-cols-1 md:grid-cols-[1.4fr_1.6fr_1fr] items-center gap-5 p-5">
                    <div class="flex items-center gap-4">
                        <img src="{{ $route->image_url }}" alt="{{ $route->bus_name }}" loading="lazy" class="h-16 w-20 shrink-0 rounded-lg object-cover">
                        <div>
                            <h2 class="font-semibold text-brand-950">{{ $route->bus_name }}</h2>
                            <p class="text-xs text-slate-500">{{ $route->operator }}</p>
                            <span class="mt-1 inline-block rounded-full bg-gold-100 px-2 py-0.5 text-[11px] font-semibold text-gold-800">{{ $route->type_label }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center gap-3">
                            <div><p class="text-lg font-bold text-brand-950">{{ $route->departure_label }}</p><p class="text-xs text-slate-500">{{ $route->from_city }}</p></div>
                            <div class="flex flex-1 flex-col items-center text-[11px] text-slate-400">
                                <span>{{ $route->duration_label }}</span>
                                <span class="relative my-1 h-px w-full bg-slate-200"><x-icon name="bus" class="absolute left-1/2 top-1/2 h-4 w-4 -translate-x-1/2 -translate-y-1/2 bg-white px-0.5 text-sky-500" /></span>
                            </div>
                            <div class="text-right"><p class="text-lg font-bold text-brand-950">{{ $route->arrival_label }}</p><p class="text-xs text-slate-500">{{ $route->to_city }}</p></div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach (array_slice($route->amenities ?? [], 0, 5) as $amenity)
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">{{ $amenity }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 md:flex-col md:items-end">
                        <div class="md:text-right">
                            <p class="text-2xl font-extrabold text-brand-700">{{ money($route->price) }}</p>
                            <p class="text-xs {{ $route->seats_left < 6 ? 'font-semibold text-rose-600' : 'text-emerald-600' }}">{{ $route->seats_left }} {{ __('seats left') }}</p>
                        </div>
                        @if ($route->seats_left >= $passengers)
                            <a href="{{ route('bus.seats', ['busRoute' => $route, 'date' => $date, 'passengers' => $passengers]) }}" class="rt-btn-gold px-5 py-2.5">{{ __('Select Seats') }}</a>
                        @else
                            <span class="rounded-full bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-400">{{ __('Sold out') }}</span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rt-card p-12 text-center">
                    <x-icon name="bus" class="mx-auto h-10 w-10 text-slate-300" />
                    <h2 class="mt-4 font-semibold text-brand-950">{{ __('No buses found on this route') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Try another date or route — or send us a request and we will arrange transport for you.') }}</p>
                    <a href="{{ route('contact', ['subject' => "Bus {$from} to {$to}"]) }}" class="rt-btn-primary mt-5">{{ __('Send a Bus Inquiry') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</x-site-layout>
