<x-site-layout :title="__('Travel Blog — Tips, Visa Updates & Destination Guides').' | '.site('name')"
    :description="__('Nepal travel tips, trekking guides, visa updates, airline news and festival travel advice from the Ram Tours team.')"
    :image="config('travel.images.viewpoint')" :breadcrumbs="[[__('Blog'), null]]">

    <x-frontend.page-hero :title="__('Travel Blog')" :eyebrow="__('Stories & guides')" :image="config('travel.images.viewpoint')"
        :subtitle="__('Travel tips, visa updates, airline news and destination guides.')" :breadcrumbs="[[__('Blog'), null]]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('blog.index') }}" class="rounded-full px-4 py-2 text-sm font-medium {{ ! request('category') ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">{{ __('All') }}</a>
                @foreach ($categories as $category)
                    <a href="{{ route('blog.index', ['category' => $category]) }}" class="rounded-full px-4 py-2 text-sm font-medium {{ request('category') === $category ? 'bg-brand-700 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-700' }}">{{ __($category) }}</a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('blog.index') }}" class="flex w-full md:w-72">
                <label for="blog-q" class="sr-only">{{ __('Search articles') }}</label>
                <input id="blog-q" name="q" value="{{ request('q') }}" placeholder="{{ __('Search articles…') }}" class="rt-input rounded-r-none">
                <button class="rounded-r-lg bg-brand-700 px-3 text-white" aria-label="{{ __('Search') }}"><x-icon name="search" class="h-4 w-4" /></button>
            </form>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($posts as $post)
                <x-frontend.blog-card :post="$post" />
            @empty
                <p class="col-span-full rt-card p-12 text-center text-slate-500">{{ __('No articles found.') }}</p>
            @endforelse
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    </div>
</x-site-layout>
