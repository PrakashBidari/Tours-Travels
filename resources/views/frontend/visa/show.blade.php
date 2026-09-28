<x-site-layout :title="$visa->title.' from Nepal | '.site('name')"
    :description="__(':title for Nepali passport holders — document checklist, processing time :time, fees and online application.', ['title' => $visa->title, 'time' => $visa->processing_time])"
    :image="$visa->image_url" :breadcrumbs="[[__('Visa Services'), route('visa.index')], [$visa->title, null]]">

    <x-frontend.page-hero :title="$visa->flag.' '.$visa->title" :image="$visa->image_url" :subtitle="__('Complete visa assistance for Nepali passport holders.')"
        :breadcrumbs="[[__('Visa Services'), route('visa.index')], [$visa->country, null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_420px] gap-8">
            <div class="space-y-8 min-w-0">
                <div class="rt-card grid grid-cols-2 sm:grid-cols-4 gap-4 p-5">
                    @foreach ([['clock', __('Processing time'), $visa->processing_time], ['calendar', __('Validity'), $visa->validity], ['sun', __('Stay duration'), $visa->stay_duration], ['visa', __('Visa type'), __($visa->type_label)]] as [$icon, $label, $value])
                        <div><x-icon :name="$icon" class="h-6 w-6 text-sky-500" /><p class="mt-2 text-[11px] uppercase text-slate-400">{{ $label }}</p><p class="font-semibold text-brand-950">{{ $value ?: '—' }}</p></div>
                    @endforeach
                </div>

                @if ($visa->description)
                    <div class="rt-prose">{!! clean($visa->description) !!}</div>
                @endif

                <section>
                    <h2 class="text-xl font-bold text-brand-950">{{ __('Document checklist') }}</h2>
                    <ul class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($visa->requirements ?? [] as $requirement)
                            <li class="rt-card flex items-start gap-3 p-4 text-sm text-slate-700"><x-icon name="document" class="h-5 w-5 shrink-0 text-brand-700" /> {{ $requirement }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-3 text-xs text-slate-400">{{ __('Requirements can change at the embassy’s discretion; our visa desk will confirm the latest list for your case.') }}</p>
                </section>

                <section class="rt-card overflow-hidden">
                    <h2 class="border-b border-slate-100 px-5 py-4 font-bold text-brand-950">{{ __('Fees per applicant') }}</h2>
                    <table class="w-full text-sm">
                        <tr class="border-b border-slate-100"><td class="px-5 py-3 text-slate-600">{{ __('Embassy / visa fee') }}</td><td class="px-5 py-3 text-right font-semibold">{{ $visa->embassy_fee > 0 ? npr($visa->embassy_fee) : __('As applicable') }}</td></tr>
                        <tr class="border-b border-slate-100"><td class="px-5 py-3 text-slate-600">{{ __('Ram Tours service charge') }}</td><td class="px-5 py-3 text-right font-semibold">{{ npr($visa->service_charge) }}</td></tr>
                        <tr class="bg-brand-50"><td class="px-5 py-3 font-semibold text-brand-950">{{ __('Total') }}</td><td class="px-5 py-3 text-right text-lg font-bold text-brand-700">{{ npr($visa->total_fee) }}</td></tr>
                    </table>
                </section>

                @if ($others->isNotEmpty())
                    <section>
                        <h2 class="text-xl font-bold text-brand-950">{{ __('Related visa services') }}</h2>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($others as $other)
                                <a href="{{ route('visa.show', $other) }}" class="rt-card flex items-center gap-3 p-4 hover:ring-brand-300"><span class="text-2xl">{{ $other->flag }}</span><span class="flex-1"><span class="block font-semibold text-brand-950">{{ $other->title }}</span><span class="text-xs text-slate-500">{{ $other->processing_time }}</span></span><x-icon name="chevron-right" class="h-4 w-4 text-slate-300" /></a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside id="apply" class="scroll-mt-28">
                <form method="POST" action="{{ route('visa.apply', $visa) }}" enctype="multipart/form-data" class="rt-card space-y-4 p-6 lg:sticky lg:top-28"
                    x-data="{ applicants: {{ (int) old('applicants', 1) }}, fee: {{ $visa->total_fee }}, discount: 0, files: [],
                        get subtotal() { return this.fee * this.applicants },
                        fmt(v) { return 'Rs. ' + Math.round(v).toLocaleString('en-IN') } }">
                    @csrf
                    <h2 class="text-lg font-bold text-brand-950">{{ __('Apply Now') }}</h2>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label for="applicants" class="rt-label">{{ __('Applicants') }}</label><input id="applicants" type="number" name="applicants" min="1" max="20" x-model.number="applicants" class="rt-input"></div>
                        <div><label for="travel_date" class="rt-label">{{ __('Planned travel') }}</label><input id="travel_date" type="date" name="travel_date" value="{{ old('travel_date', request('date')) }}" min="{{ now()->toDateString() }}" class="rt-input"></div>
                    </div>

                    <x-frontend.traveler-fields :passport-required="true" service="visa" amount-expression="subtotal" />

                    <div>
                        <span class="rt-label">{{ __('Upload documents (PDF / JPG / PNG, max 5 MB each)') }}</span>
                        <label class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 text-center hover:border-brand-400">
                            <x-icon name="upload" class="h-7 w-7 text-brand-600" />
                            <span class="mt-2 text-sm font-medium text-brand-700">{{ __('Choose files') }}</span>
                            <span class="text-xs text-slate-400">{{ __('Passport, photo, bank statement…') }}</span>
                            <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp" class="sr-only" @change="files = Array.from($event.target.files).map(f => f.name)">
                        </label>
                        <ul class="mt-2 space-y-1 text-xs text-slate-600"><template x-for="name in files" :key="name"><li class="flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span><span x-text="name"></span></li></template></ul>
                        <x-input-error for="documents" class="mt-1" />
                        <x-input-error for="documents.0" class="mt-1" />
                    </div>

                    <dl class="space-y-1.5 border-t border-slate-100 pt-4 text-sm">
                        <div class="flex justify-between text-slate-600"><dt>{{ npr($visa->total_fee) }} × <span x-text="applicants"></span></dt><dd x-text="fmt(subtotal)"></dd></div>
                        <div class="flex justify-between text-emerald-600" x-show="discount > 0" x-cloak><dt>{{ __('Coupon discount') }}</dt><dd x-text="'- ' + fmt(discount)"></dd></div>
                        <div class="flex justify-between pt-2 text-base font-bold text-brand-950"><dt>{{ __('Total') }}</dt><dd x-text="fmt(Math.max(0, subtotal - discount))"></dd></div>
                    </dl>
                    <button class="rt-btn-gold w-full">{{ __('Submit Application') }}</button>
                    <p class="text-center text-xs text-slate-400">{{ __('Your documents are stored securely and only accessible to our visa team.') }}</p>
                </form>
            </aside>
        </div>
    </div>
</x-site-layout>
