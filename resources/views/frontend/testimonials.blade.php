<x-site-layout :title="__('Customer Reviews').' | '.site('name')" :description="__('Read reviews from travelers who booked tours, flights, buses, cars and visas with Ram Tours & Travel.')" :breadcrumbs="[[__('Reviews'), null]]"
    :schema="$reviews->isNotEmpty() ? [['@context' => 'https://schema.org', '@type' => 'TravelAgency', 'name' => site('name'), 'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => $average, 'reviewCount' => $reviews->count()]]] : []">
    <x-frontend.page-hero :title="__('Happy Travelers')" :eyebrow="__('Traveler’s stories')" :image="config('travel.images.traveler')" :subtitle="__('Real reviews from real travelers.')" :breadcrumbs="[[__('Reviews'), null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="rt-card flex flex-wrap items-center justify-between gap-6 p-6">
            <div class="flex items-center gap-4">
                <p class="text-5xl font-extrabold text-brand-700">{{ number_format($average, 1) }}</p>
                <div>
                    <div class="flex">@for ($s = 1; $s <= 5; $s++)<x-icon name="star" :solid="true" class="h-5 w-5 {{ $s <= round($average) ? 'text-gold-500' : 'text-slate-200' }}" />@endfor</div>
                    <p class="text-sm text-slate-500">{{ __('Based on :count reviews', ['count' => $reviews->count()]) }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 text-sm">
                @foreach (\App\Models\Testimonial::SOURCES as $key => $label)
                    @php $count = $reviews->where('source', $key)->count(); @endphp
                    @if ($count)<span class="rt-chip">{{ $label }} · {{ $count }}</span>@endif
                @endforeach
            </div>
        </div>

        <div class="mt-8 columns-1 md:columns-2 lg:columns-3 gap-6 [&>*]:mb-6">
            @foreach ($reviews as $review)
                <figure class="rt-card break-inside-avoid p-6">
                    <div class="flex">@for ($s = 1; $s <= 5; $s++)<x-icon name="star" :solid="true" class="h-4 w-4 {{ $s <= $review->rating ? 'text-gold-500' : 'text-slate-200' }}" />@endfor</div>
                    <blockquote class="mt-3 text-sm leading-relaxed text-slate-600">&ldquo;{{ $review->content }}&rdquo;</blockquote>
                    @if ($review->video_url)
                        <a href="{{ $review->video_url }}" class="glightbox mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-700" data-type="video"><x-icon name="play" :solid="true" class="h-4 w-4" /> {{ __('Watch video review') }}</a>
                    @endif
                    <figcaption class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4">
                        <img src="{{ $review->avatar_url }}" alt="{{ $review->name }}" loading="lazy" class="h-10 w-10 rounded-full object-cover">
                        <div class="flex-1"><p class="text-sm font-semibold text-brand-950">{{ $review->name }}</p><p class="text-xs text-slate-500">{{ $review->location }}</p></div>
                        <span class="text-[11px] font-medium text-slate-400">{{ \App\Models\Testimonial::SOURCES[$review->source] ?? '' }}</span>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        <section class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            <div>
                <x-frontend.section-heading :eyebrow="__('Share your experience')" :title="__('Write a Review')" :subtitle="__('Travelled with us? We’d love to hear about it. Reviews are published after a quick check by our team.')" />
            </div>
            <form method="POST" action="{{ route('testimonials.store') }}" class="rt-card grid grid-cols-1 sm:grid-cols-2 gap-4 p-6" x-data="{ rating: {{ (int) old('rating', 5) }} }">
                @csrf
                <div><label for="rv-name" class="rt-label">{{ __('Your name') }}</label><input id="rv-name" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="rt-input"></div>
                <div><label for="rv-location" class="rt-label">{{ __('City / country') }}</label><input id="rv-location" name="location" value="{{ old('location') }}" class="rt-input"></div>
                <div class="sm:col-span-2">
                    <span class="rt-label">{{ __('Rating') }}</span>
                    <input type="hidden" name="rating" :value="rating">
                    <div class="flex gap-1">
                        @for ($s = 1; $s <= 5; $s++)
                            <button type="button" @click="rating = {{ $s }}" :class="rating >= {{ $s }} ? 'text-gold-500' : 'text-slate-300'" aria-label="{{ $s }} {{ __('stars') }}"><x-icon name="star" :solid="true" class="h-7 w-7" /></button>
                        @endfor
                    </div>
                </div>
                <div class="sm:col-span-2"><label for="rv-content" class="rt-label">{{ __('Your review') }}</label><textarea id="rv-content" name="content" rows="4" required minlength="20" class="rt-input">{{ old('content') }}</textarea><x-input-error for="content" class="mt-1" /></div>
                <div class="sm:col-span-2"><button class="rt-btn-primary">{{ __('Submit Review') }}</button></div>
            </form>
        </section>
    </div>
</x-site-layout>
