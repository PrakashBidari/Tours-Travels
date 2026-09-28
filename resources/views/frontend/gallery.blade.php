<x-site-layout :title="__('Gallery — Photos & Videos').' | '.site('name')" :description="__('Photos and videos from Nepal tours, treks and international holidays with Ram Tours & Travel.')" :breadcrumbs="[[__('Gallery'), null]]">
    <x-frontend.page-hero :title="__('Gallery')" :eyebrow="__('Customer memories')" :image="config('travel.images.group')" :subtitle="__('Moments captured by our travelers across Nepal and the world.')" :breadcrumbs="[[__('Gallery'), null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('gallery.index') }}" class="rounded-full px-4 py-2 text-sm font-medium {{ ! request('album') ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">{{ __('All') }}</a>
            @foreach ($albums as $album)
                <a href="{{ route('gallery.index', ['album' => $album]) }}" class="rounded-full px-4 py-2 text-sm font-medium {{ request('album') === $album ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-700' }}">{{ __($album) }}</a>
            @endforeach
        </div>

        <div class="mt-8 columns-1 sm:columns-2 lg:columns-3 gap-4 [&>*]:mb-4">
            @forelse ($items as $item)
                <a href="{{ $item->type === 'video' ? $item->embed_url : $item->url }}" class="glightbox group relative block break-inside-avoid overflow-hidden rounded-2xl" data-gallery="gallery" data-title="{{ $item->title }}" @if ($item->type === 'video') data-type="video" @endif>
                    <img src="{{ $item->thumbnail }}" alt="{{ $item->title }}" loading="lazy" class="w-full object-cover transition duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 flex items-end bg-gradient-to-t from-brand-950/70 via-transparent to-transparent p-4 opacity-0 transition group-hover:opacity-100">
                        <span class="text-sm font-semibold text-white">{{ $item->title }}</span>
                    </div>
                    @if ($item->type === 'video')
                        <span class="absolute inset-0 flex items-center justify-center"><span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90 text-brand-700 shadow-lg"><x-icon name="play" :solid="true" class="h-6 w-6" /></span></span>
                    @endif
                </a>
            @empty
                <p class="rt-card p-12 text-center text-slate-500">{{ __('No photos yet.') }}</p>
            @endforelse
        </div>
    </div>
</x-site-layout>
