<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Gallery — Photos & Videos</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('dashboard.gallery.store') }}" enctype="multipart/form-data" x-data="{ type: 'image' }"
                class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                @csrf
                <div>
                    <label for="g-album" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Album</label>
                    <input id="g-album" name="album" list="albums" value="{{ old('album', request('album', 'Destinations')) }}" required class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                    <datalist id="albums">@foreach ($albums->merge(['Destinations', 'Trekking', 'International', 'Customer Memories', 'Videos', 'Office'])->unique() as $album)<option value="{{ $album }}">@endforeach</datalist>
                </div>
                <div>
                    <label for="g-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title / caption</label>
                    <input id="g-title" name="title" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                </div>
                <div>
                    <label for="g-photos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload photos</label>
                    <input id="g-photos" type="file" name="photos[]" multiple accept="image/*" class="mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                </div>
                <div>
                    <label for="g-url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">…or URL</label>
                    <div class="mt-1 flex gap-1">
                        <select name="type" x-model="type" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm"><option value="image">Image</option><option value="video">YouTube</option></select>
                        <input id="g-url" name="url" type="url" :placeholder="type === 'video' ? 'https://youtube.com/watch?v=…' : 'https://…jpg'" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 text-sm">
                    </div>
                </div>
                <x-button class="justify-center">Add to gallery</x-button>
            </form>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.gallery.index') }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ ! request('album') ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">All</a>
                @foreach ($albums as $album)
                    <a href="{{ route('dashboard.gallery.index', ['album' => $album]) }}" class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('album') === $album ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">{{ $album }}</a>
                @endforeach
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @forelse ($items as $item)
                    <div class="overflow-hidden rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                        <div class="relative aspect-square">
                            <img src="{{ $item->thumbnail }}" alt="" class="h-full w-full object-cover">
                            @if ($item->type === 'video')<span class="absolute left-2 top-2 rounded bg-red-600 px-1.5 py-0.5 text-[10px] font-bold text-white">VIDEO</span>@endif
                        </div>
                        <form method="POST" action="{{ route('dashboard.gallery.update', $item) }}" class="space-y-1.5 p-2">
                            @csrf @method('PUT')
                            <input name="title" value="{{ $item->title }}" placeholder="Title" class="w-full rounded border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 px-2 py-1 text-xs">
                            <div class="flex gap-1">
                                <input name="album" value="{{ $item->album }}" class="w-full rounded border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 px-2 py-1 text-xs">
                                <input name="position" type="number" value="{{ $item->position }}" title="Position" class="w-14 rounded border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 px-1 py-1 text-xs">
                            </div>
                            <div class="flex justify-between text-xs font-medium">
                                <button class="text-indigo-600">Save</button>
                                <button type="button" data-confirm-url="{{ route('dashboard.gallery.destroy', $item) }}" data-confirm="Remove this item from the gallery?" class="text-red-600">Delete</button>
                            </div>
                        </form>
                    </div>
                @empty
                    <p class="col-span-full py-12 text-center text-gray-400">The gallery is empty.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
