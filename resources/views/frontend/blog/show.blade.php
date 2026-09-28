<x-site-layout :title="($post->meta_title ?: $post->title).' | '.site('name')" :description="$post->meta_description ?: $post->excerpt" :image="$post->image_url"
    :breadcrumbs="[[__('Blog'), route('blog.index')], [$post->title, null]]"
    :schema="[['@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $post->title, 'image' => $post->image_url, 'datePublished' => $post->published_at?->toAtomString(), 'dateModified' => $post->updated_at->toAtomString(), 'author' => ['@type' => 'Organization', 'name' => site('name')], 'publisher' => ['@type' => 'Organization', 'name' => site('name')], 'description' => $post->excerpt, 'mainEntityOfPage' => route('blog.show', $post)]]">

    <article>
        <header class="relative overflow-hidden bg-brand-950">
            <img src="{{ $post->image_url }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-950/60 to-transparent"></div>
            <div class="relative max-w-3xl mx-auto px-4 sm:px-6 py-20 text-center text-white">
                <a href="{{ route('blog.index', ['category' => $post->category]) }}" class="rounded-full bg-gold-500 px-3 py-1 text-xs font-bold text-brand-950">{{ __($post->category) }}</a>
                <h1 class="mt-4 text-3xl sm:text-4xl font-bold leading-tight">{{ $post->title }}</h1>
                <p class="mt-4 text-sm text-brand-100">{{ $post->published_at?->format('F d, Y') }} · {{ $post->reading_time }} {{ __('min read') }} · {{ number_format($post->views) }} {{ __('views') }}</p>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-12">
            <div class="min-w-0">
                @if ($post->excerpt)<p class="text-lg font-medium leading-relaxed text-slate-600">{{ $post->excerpt }}</p>@endif
                <div class="rt-prose mt-6">{!! clean($post->content) !!}</div>

                <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <span class="text-sm font-semibold text-slate-600">{{ __('Share') }}:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post)) }}" target="_blank" rel="noopener" class="rt-chip">Facebook</a>
                    <a href="https://wa.me/?text={{ urlencode($post->title.' '.route('blog.show', $post)) }}" target="_blank" rel="noopener" class="rt-chip">WhatsApp</a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('blog.show', $post)) }}" target="_blank" rel="noopener" class="rt-chip">X / Twitter</a>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-2xl bg-gradient-to-br from-brand-700 to-sky-500 p-6 text-white">
                    <h2 class="text-lg font-bold">{{ __('Plan your trip with experts') }}</h2>
                    <p class="mt-1 text-sm text-brand-100">{{ __('Get a free itinerary and quote from our travel consultants.') }}</p>
                    <a href="{{ route('contact', ['subject' => 'Trip planning: '.$post->title]) }}" class="rt-btn-gold mt-4 px-5 py-2.5">{{ __('Get a Free Quote') }}</a>
                </div>
                @if ($recent->isNotEmpty())
                    <div class="rt-card p-5">
                        <h2 class="font-semibold text-brand-950">{{ __('Recent articles') }}</h2>
                        <ul class="mt-4 space-y-4">
                            @foreach ($recent as $item)
                                <li><a href="{{ route('blog.show', $item) }}" class="group flex gap-3"><img src="{{ $item->image_url }}" alt="" loading="lazy" class="h-16 w-20 shrink-0 rounded-lg object-cover"><span><span class="block text-sm font-medium text-brand-950 group-hover:text-brand-700 line-clamp-2">{{ $item->title }}</span><span class="text-xs text-slate-400">{{ $item->published_at?->format('M d, Y') }}</span></span></a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </article>
</x-site-layout>
