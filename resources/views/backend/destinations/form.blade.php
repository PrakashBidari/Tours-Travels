<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ $destination->exists ? 'Edit Destination' : 'New Destination' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ $destination->exists ? route('dashboard.destinations.update', $destination) : route('dashboard.destinations.store') }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    @if ($destination->exists) @method('PUT') @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-label for="name" value="Destination name" />
                            <x-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $destination->name)" required />
                        </div>

                        <div>
                            <x-label for="city" value="City (optional)" />
                            <x-input id="city" name="city" class="block mt-1 w-full" :value="old('city', $destination->city)" />
                        </div>

                        <div>
                            <x-label for="country" value="Country" />
                            <x-input id="country" name="country" class="block mt-1 w-full" :value="old('country', $destination->country)" required />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="description" value="Description (optional)" />
                            <textarea id="description" name="description" rows="6" data-ckeditor class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">{{ old('description', $destination->description) }}</textarea>
                        </div>

                        <div>
                            <x-label for="image" value="Upload image (optional)" />
                            <input id="image" name="image" type="file" accept="image/*" data-preview="#image-preview" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                        </div>

                        <div>
                            <x-label for="image_url" value="…or image URL (optional)" />
                            <x-input id="image_url" name="image_url" class="block mt-1 w-full" :value="old('image_url')" placeholder="https://example.com/photo.jpg" />
                        </div>

                        <div class="sm:col-span-2">
                            <div id="image-preview" class="flex flex-wrap gap-2">
                                @if ($destination->exists && $destination->image)
                                    <img src="{{ $destination->image_url }}" class="w-[200px] h-[140px] object-cover rounded-lg ring-1 ring-gray-900/10" alt="Current image">
                                @endif
                            </div>
                            @if ($destination->exists && $destination->image)
                                <p class="mt-1 text-xs text-gray-400">Current image &mdash; upload a new one or set a URL above to replace it.</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 mt-2">
                            <x-checkbox id="is_featured" name="is_featured" value="1" :checked="old('is_featured', $destination->is_featured)" />
                            <x-label for="is_featured" value="Feature on home page" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('dashboard.destinations.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                        <x-button>{{ $destination->exists ? 'Save changes' : 'Create destination' }}</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
