@php
    $faqSchema = [[
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->flatten()->map(fn ($faq) => ['@type' => 'Question', 'name' => $faq->question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer]])->values()->all(),
    ]];
@endphp
<x-site-layout :title="__('Frequently Asked Questions').' | '.site('name')" :description="__('Answers about tour packages, visas, flight and bus tickets, payments and refunds.')" :breadcrumbs="[[__('FAQ'), null]]" :schema="$faqSchema">
    <x-frontend.page-hero :title="__('Frequently Asked Questions')" :subtitle="__('Everything you need to know before you travel with us.')" :breadcrumbs="[[__('FAQ'), null]]" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ open: null }">
        @foreach ($faqs as $category => $items)
            <section class="{{ $loop->first ? '' : 'mt-10' }}">
                <h2 class="flex items-center gap-2 text-lg font-bold text-brand-950"><span class="h-0.5 w-6 rounded bg-gold-500"></span>{{ __($category) }}</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($items as $faq)
                        <div class="rt-card overflow-hidden">
                            <button type="button" @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}" class="flex w-full items-center justify-between gap-4 p-5 text-left" :aria-expanded="open === {{ $faq->id }}">
                                <span class="font-semibold text-brand-950">{{ $faq->question }}</span>
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700 transition" :class="open === {{ $faq->id }} && 'rotate-45 bg-brand-700 text-white'">+</span>
                            </button>
                            <div x-show="open === {{ $faq->id }}" x-collapse x-cloak class="px-5 pb-5 text-sm leading-relaxed text-slate-600">{{ $faq->answer }}</div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach

        <div class="mt-12 rounded-2xl bg-brand-50 p-6 text-center">
            <h2 class="font-semibold text-brand-950">{{ __('Still have questions?') }}</h2>
            <p class="mt-1 text-sm text-slate-600">{{ __('Our travel consultants are happy to help.') }}</p>
            <div class="mt-4 flex flex-wrap justify-center gap-3">
                <a href="{{ route('contact') }}" class="rt-btn-primary px-5 py-2.5">{{ __('Contact Us') }}</a>
                <a href="https://wa.me/{{ site('whatsapp') }}" target="_blank" rel="noopener" class="rt-btn bg-[#25D366] px-5 py-2.5 text-white">WhatsApp</a>
            </div>
        </div>
    </div>
</x-site-layout>
