<x-site-layout :title="__('Compare Packages').' | '.site('name')" :breadcrumbs="[[__('Tour Packages'), route('tours.index')], [__('Compare'), null]]">
    <x-frontend.page-hero :title="__('Compare Packages')" :subtitle="__('See your shortlisted packages side by side.')" :breadcrumbs="[[__('Tour Packages'), route('tours.index')], [__('Compare'), null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @if ($tours->isEmpty())
            <div class="rt-card p-12 text-center">
                <x-icon name="swap" class="mx-auto h-10 w-10 text-slate-300" />
                <h2 class="mt-4 font-semibold text-brand-950">{{ __('No packages to compare yet') }}</h2>
                <p class="mt-1 text-sm text-slate-500">{{ __('Click “Compare” on up to three packages to see them here.') }}</p>
                <a href="{{ route('tours.index') }}" class="rt-btn-primary mt-5">{{ __('Browse packages') }}</a>
            </div>
        @else
            @php
                $rows = [
                    __('Price per person') => fn ($t) => npr($t->final_price),
                    __('Duration') => fn ($t) => $t->duration_label,
                    __('Destination') => fn ($t) => $t->destination.', '.$t->country,
                    __('Category') => fn ($t) => $t->category_label,
                    __('Best season') => fn ($t) => __($t->season_label),
                    __('Accommodation') => fn ($t) => $t->hotel ?: '—',
                    __('Meals') => fn ($t) => $t->meals ?: '—',
                    __('Transport') => fn ($t) => $t->transport ?: '—',
                    __('Difficulty / altitude') => fn ($t) => trim(($t->difficulty ?: '').' '.($t->max_altitude ?: '')) ?: '—',
                    __('Rating') => fn ($t) => number_format((float) $t->rating, 1).' ★ ('.$t->review_count.')',
                ];
            @endphp
            <div class="rt-card overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead>
                        <tr>
                            <th class="w-44 p-4"></th>
                            @foreach ($tours as $tour)
                                <th class="p-4 text-left align-top">
                                    <img src="{{ $tour->main_image }}" alt="" class="h-32 w-full rounded-xl object-cover">
                                    <a href="{{ route('tours.show', $tour) }}" class="mt-3 block font-semibold text-brand-950 hover:text-brand-700">{{ $tour->title }}</a>
                                    <button type="button" data-compare-remove="{{ $tour->slug }}" class="mt-1 text-xs font-medium text-rose-500">{{ __('Remove') }}</button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rows as $label => $value)
                            <tr class="odd:bg-slate-50/60">
                                <th class="p-4 text-left font-medium text-slate-500">{{ $label }}</th>
                                @foreach ($tours as $tour)
                                    <td class="p-4 text-slate-700">{{ $value($tour) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <th class="p-4 text-left font-medium text-slate-500">{{ __('Included') }}</th>
                            @foreach ($tours as $tour)
                                <td class="p-4 align-top"><ul class="space-y-1">@foreach ($tour->includes ?? [] as $item)<li class="flex gap-1.5 text-slate-600"><x-icon name="check" class="h-4 w-4 shrink-0 text-emerald-500" />{{ $item }}</li>@endforeach</ul></td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="p-4"></th>
                            @foreach ($tours as $tour)
                                <td class="p-4"><a href="{{ route('tours.show', $tour) }}#book" class="rt-btn-gold w-full py-2.5">{{ __('Book Now') }}</a></td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-site-layout>
