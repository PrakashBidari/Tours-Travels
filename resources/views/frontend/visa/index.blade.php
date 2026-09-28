<x-site-layout :title="__('Visa Services in Nepal — Tourist, Student & Work Visas').' | '.site('name')"
    :description="__('Visa assistance for Nepali passport holders: UAE, Thailand, Malaysia, Singapore, Schengen, Australia, UK, USA, Japan and more. Document checklist, fees and processing time.')"
    :image="config('travel.images.visa')" :breadcrumbs="[[__('Visa Services'), null]]">

    <x-frontend.page-hero :title="__('Visa Services')" :eyebrow="__('Your visa, our support')" :image="config('travel.images.visa')"
        :subtitle="__('Tourist, visit, student and work visa processing with complete documentation support.')" :breadcrumbs="[[__('Visa Services'), null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <form method="GET" action="{{ route('visa.index') }}" class="rt-card grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] items-end gap-3 p-4 sm:p-5">
            <div>
                <label for="v-country" class="rt-label">{{ __('Country') }}</label>
                <select id="v-country" name="country" class="rt-input">
                    <option value="">{{ __('All countries') }}</option>
                    @foreach ($countries as $country)<option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>@endforeach
                </select>
            </div>
            <div>
                <label for="v-type" class="rt-label">{{ __('Visa type') }}</label>
                <select id="v-type" name="type" class="rt-input">
                    <option value="">{{ __('All types') }}</option>
                    @foreach (config('travel.visa_types') as $key => $label)<option value="{{ $key }}" @selected(request('type') === $key)>{{ __($label) }}</option>@endforeach
                </select>
            </div>
            <button class="rt-btn-primary h-[42px] py-0"><x-icon name="search" class="h-4 w-4" /> {{ __('Find Visa') }}</button>
        </form>

        <div class="mt-6 flex flex-wrap gap-2">
            @foreach (config('travel.visa_types') as $key => $label)
                <a href="{{ route('visa.index', ['type' => $key]) }}" class="rounded-full px-4 py-2 text-sm font-medium transition {{ request('type') === $key ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-700' }}">{{ __($label) }}</a>
            @endforeach
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($visas as $visa)
                <a href="{{ route('visa.show', $visa) }}" class="rt-card group flex flex-col overflow-hidden transition hover:-translate-y-1 hover:shadow-glow">
                    <div class="relative aspect-[16/9] overflow-hidden">
                        <img src="{{ $visa->image_url }}" alt="{{ $visa->country }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-950/80 to-transparent"></div>
                        <div class="absolute bottom-3 left-4 flex items-center gap-2 text-white">
                            <span class="text-3xl leading-none">{{ $visa->flag }}</span>
                            <span><span class="block text-lg font-bold">{{ $visa->country }}</span><span class="text-xs text-white/80">{{ __($visa->type_label) }}</span></span>
                        </div>
                    </div>
                    <div class="grid flex-1 grid-cols-2 gap-3 p-5 text-sm">
                        <div><p class="text-[11px] uppercase text-slate-400">{{ __('Processing') }}</p><p class="font-medium text-brand-950">{{ $visa->processing_time }}</p></div>
                        <div><p class="text-[11px] uppercase text-slate-400">{{ __('Stay') }}</p><p class="font-medium text-brand-950">{{ $visa->stay_duration }}</p></div>
                        <div class="col-span-2 flex items-end justify-between border-t border-slate-100 pt-3">
                            <div><p class="text-[11px] text-slate-400">{{ __('Total fee from') }}</p><p class="text-lg font-bold text-brand-700">{{ money($visa->total_fee) }}</p></div>
                            <span class="inline-flex items-center gap-1 text-sm font-semibold text-brand-700">{{ __('Apply Now') }} <x-icon name="arrow-right" class="h-4 w-4 transition group-hover:translate-x-1" /></span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="rt-card col-span-full p-12 text-center">
                    <x-icon name="visa" class="mx-auto h-10 w-10 text-slate-300" />
                    <h2 class="mt-4 font-semibold text-brand-950">{{ __('No visa services found') }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Ask our visa desk about any country not listed here.') }}</p>
                    <a href="{{ route('contact', ['subject' => 'Visa inquiry']) }}" class="rt-btn-primary mt-5">{{ __('Ask the Visa Desk') }}</a>
                </div>
            @endforelse
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach ([['document', __('Share documents'), __('Upload scans online or visit our office.')], ['check-circle', __('We verify'), __('Our visa desk checks every document within one working day.')], ['upload', __('We submit'), __('Forms, appointment booking and embassy submission.')], ['badge', __('Visa approved'), __('Track status and collect your passport.')]] as $i => [$icon, $title, $text])
                <div class="text-center">
                    <span class="relative mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-700 text-white"><x-icon :name="$icon" class="h-6 w-6" /><span class="absolute -right-1 -top-1 flex h-6 w-6 items-center justify-center rounded-full bg-gold-500 text-xs font-bold text-brand-950">{{ $i + 1 }}</span></span>
                    <h3 class="mt-4 font-semibold text-brand-950">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-site-layout>
