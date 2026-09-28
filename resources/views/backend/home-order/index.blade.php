<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Homepage Order') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Drag items to control the order they appear in on the home page and, for destinations, on the public
                destinations page. Items you haven't reordered fall back to newest-first. Use the
                <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 -mt-0.5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                menu on an item for precise up/down/top/bottom moves instead of dragging.
            </p>

            <!-- Destinations order -->
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Popular destinations</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Controls "Popular destinations" on the home page and the order on the public destinations page.</p>

                <div class="mt-4 relative max-w-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" data-list-search="#destinations-order-list" placeholder="Search destinations…"
                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 pl-9 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <ul id="destinations-order-list" data-sortable-list data-sortable-url="{{ route('dashboard.home-order.destinations') }}"
                    class="mt-3 space-y-2 overflow-y-auto pr-1" style="height: 1024px;">
                    @forelse ($destinations as $destination)
                        <li data-id="{{ $destination->id }}" data-name="{{ strtolower($destination->name) }}" draggable="true"
                            class="relative flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 cursor-move select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6h16.5" />
                            </svg>
                            <img src="{{ $destination->image_url }}" class="h-10 w-14 object-cover rounded shrink-0" alt="">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $destination->name }}</div>
                                <div class="text-xs text-gray-400">{{ $destination->city ? $destination->city.', ' : '' }}{{ $destination->country }}</div>
                            </div>
                            <span data-position-badge class="shrink-0 text-xs font-medium text-gray-400">{{ $destination->position ? '#'.$destination->position : 'Default' }}</span>

                            <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" draggable="false" @click.stop="open = !open"
                                    class="h-8 w-8 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700/60">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                </button>

                                <div x-show="open" x-cloak x-transition draggable="false" @click.stop
                                    class="absolute right-0 top-full mt-1 z-20 flex flex-col gap-0.5 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-900/10 dark:ring-white/10 p-1.5">
                                    <button type="button" title="Move to top" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $destination->id }}', 'top'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 18.75l7.5-7.5 7.5 7.5M4.5 12.75l7.5-7.5 7.5 7.5" /></svg>
                                    </button>
                                    <button type="button" title="Move up" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $destination->id }}', 'up'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                                    </button>
                                    <button type="button" title="Move down" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $destination->id }}', 'down'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <button type="button" title="Move to bottom" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $destination->id }}', 'bottom'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 5.25l-7.5 7.5-7.5-7.5M19.5 11.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 py-4 text-center">No approved destinations yet.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Packages order -->
            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Featured stays</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Controls the "Featured stays" section on the home page. Only packages marked as featured are listed here &mdash; mark a package as featured from its edit form first.</p>

                <div class="mt-4 relative max-w-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" data-list-search="#packages-order-list" placeholder="Search packages…"
                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 pl-9 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <ul id="packages-order-list" data-sortable-list data-sortable-url="{{ route('dashboard.home-order.packages') }}"
                    class="mt-3 space-y-2 overflow-y-auto pr-1" style="height: 1024px;">
                    @forelse ($packages as $package)
                        <li data-id="{{ $package->id }}" data-name="{{ strtolower($package->name) }}" draggable="true"
                            class="relative flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 cursor-move select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6h16.5" />
                            </svg>
                            <img src="{{ $package->main_image }}" class="h-10 w-14 object-cover rounded shrink-0" alt="">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $package->name }}</div>
                                <div class="text-xs text-gray-400">{{ $package->city }}, {{ $package->country }}</div>
                            </div>
                            <span data-position-badge class="shrink-0 text-xs font-medium text-gray-400">{{ $package->position ? '#'.$package->position : 'Default' }}</span>

                            <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                                <button type="button" draggable="false" @click.stop="open = !open"
                                    class="h-8 w-8 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:text-gray-200 dark:hover:bg-gray-700/60">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="1.5" /><circle cx="12" cy="12" r="1.5" /><circle cx="12" cy="19" r="1.5" /></svg>
                                </button>

                                <div x-show="open" x-cloak x-transition draggable="false" @click.stop
                                    class="absolute right-0 top-full mt-1 z-20 flex flex-col gap-0.5 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-900/10 dark:ring-white/10 p-1.5">
                                    <button type="button" title="Move to top" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $package->id }}', 'top'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 18.75l7.5-7.5 7.5 7.5M4.5 12.75l7.5-7.5 7.5 7.5" /></svg>
                                    </button>
                                    <button type="button" title="Move up" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $package->id }}', 'up'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                                    </button>
                                    <button type="button" title="Move down" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $package->id }}', 'down'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <button type="button" title="Move to bottom" draggable="false"
                                        @click="window.reorderListItem($el.closest('[data-sortable-list]'), '{{ $package->id }}', 'bottom'); open = false"
                                        class="h-8 w-8 flex items-center justify-center rounded-md text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 5.25l-7.5 7.5-7.5-7.5M19.5 11.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 py-4 text-center">No featured packages yet.</li>
                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
