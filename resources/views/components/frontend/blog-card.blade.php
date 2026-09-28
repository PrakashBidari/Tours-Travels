@props(['post'])

<article {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-2xl bg-white shadow-card ring-1 ring-slate-900/5 transition hover:-translate-y-1']) }}>
    <a href="{{ route('blog.show', $post) }}" class="block aspect-[16/10] overflow-hidden">
        <img src="{{ $post->image_url }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center gap-3 text-xs text-slate-500">
            <a href="{{ route('blog.index', ['category' => $post->category]) }}" class="rounded-full bg-brand-50 px-2.5 py-1 font-semibold text-brand-700">{{ __($post->category) }}</a>
            <span>{{ $post->published_at?->format('M d, Y') }}</span>
            <span>&middot; {{ $post->reading_time }} {{ __('min read') }}</span>
        </div>
        <h3 class="mt-3 font-semibold leading-snug text-brand-950 group-hover:text-brand-700">
            <a href="{{ route('blog.show', $post) }}">{{ $post->title }}</a>
        </h3>
        <p class="mt-2 text-sm text-slate-500 line-clamp-2">{{ $post->excerpt }}</p>
        <a href="{{ route('blog.show', $post) }}" class="mt-auto pt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700">{{ __('Read more') }} <x-icon name="arrow-right" class="h-4 w-4" /></a>
    </div>
</article>
