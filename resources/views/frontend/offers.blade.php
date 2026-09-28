<x-site-layout :title="__('Offers & Deals — Festival, Early Bird & Holiday Discounts').' | '.site('name')"
    :description="__('Dashain, Tihar, New Year and early-bird travel offers with coupon codes on tours, buses and car rental.')"
    :image="config('travel.images.maldives')" :breadcrumbs="[[__('Offers & Deals'), null]]">

    <x-frontend.page-hero :title="__('Offers & Deals')" :eyebrow="__('Save more on every trip')" :image="config('travel.images.maldives')"
        :subtitle="__('Festival specials, early-bird discounts and coupon codes — use them at checkout.')" :breadcrumbs="[[__('Offers & Deals'), null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if ($offers->isEmpty())
            <div class="rt-card p-12 text-center">
                <x-icon name="tag" class="mx-auto h-10 w-10 text-slate-300" />
                <h2 class="mt-4 font-semibold text-brand-950">{{ __('No live offers right now') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('Subscribe to our newsletter to hear about the next deal first.') }}</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($offers as $offer)
                    <article class="rt-card group grid grid-cols-1 sm:grid-cols-[220px_1fr] overflow-hidden"
                        x-data="{ end: {{ $offer->expires_at ? $offer->expires_at->timestamp * 1000 : 'null' }}, now: Date.now(), timer: null,
                            get left() { return Math.max(0, (this.end ?? 0) - this.now) },
                            part(ms) { return String(Math.floor(ms)).padStart(2, '0') } }"
                        x-init="if (end) timer = setInterval(() => now = Date.now(), 1000)">
                        <div class="relative h-48 sm:h-full overflow-hidden">
                            <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <span class="absolute left-3 top-3 rounded-full bg-gold-500 px-3 py-1 text-sm font-extrabold text-brand-950 shadow">{{ $offer->discount_label }}</span>
                        </div>
                        <div class="flex flex-col p-6">
                            @if ($offer->badge)<span class="rt-chip w-fit">{{ $offer->badge }}</span>@endif
                            <h2 class="mt-2 text-lg font-bold text-brand-950">{{ $offer->title }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $offer->description }}</p>
                            <p class="mt-2 text-xs text-slate-400">
                                {{ __('Valid on') }}: {{ $offer->applies_to === 'all' ? __('all services') : config('travel.service_types.'.$offer->applies_to) }}
                                @if ($offer->min_amount > 0) · {{ __('Min.') }} {{ npr($offer->min_amount) }}@endif
                                @if ($offer->max_discount) · {{ __('Max discount') }} {{ npr($offer->max_discount) }}@endif
                            </p>

                            @if ($offer->expires_at)
                                <div class="mt-4 flex gap-2 text-center">
                                    @foreach ([['d', 86400000, 1e9, __('Days')], ['h', 3600000, 24, __('Hrs')], ['m', 60000, 60, __('Min')], ['s', 1000, 60, __('Sec')]] as [$unit, $ms, $mod, $label])
                                        <div class="w-14 rounded-lg bg-brand-950 py-1.5 text-white">
                                            <span class="block font-mono text-lg font-bold" x-text="part((left / {{ $ms }}) % {{ $mod }})"></span>
                                            <span class="text-[10px] uppercase text-brand-200">{{ $label }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-auto flex flex-wrap items-center gap-3 pt-5">
                                @if ($offer->code)
                                    <button type="button" x-data @click="navigator.clipboard.writeText(@js($offer->code)); AppAlert.success(@js(__('Coupon code copied!')))"
                                        class="rounded-lg border-2 border-dashed border-gold-500 bg-gold-50 px-4 py-2 font-mono text-sm font-bold text-gold-800 hover:bg-gold-100" title="{{ __('Copy code') }}">{{ $offer->code }}</button>
                                @endif
                                <a href="{{ $offer->link_url ?: route('tours.index') }}" class="rt-btn-primary px-5 py-2">{{ __('Book Now') }}</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @if ($deals->isNotEmpty())
            <section class="mt-16">
                <x-frontend.section-heading :eyebrow="__('On sale now')" :title="__('Discounted Packages')" :link="route('tours.index')" />
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($deals as $tour)
                        <x-frontend.tour-card :tour="$tour" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-site-layout>
