<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ $package->exists ? 'Edit Package' : 'New Package' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ $package->exists ? route('dashboard.packages.update', $package) : route('dashboard.packages.store') }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf
                    @if ($package->exists) @method('PUT') @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-label for="name" value="Package name" />
                            <x-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $package->name)" required />
                        </div>

                        <div>
                            <x-label for="user_id" value="Vendor (optional)" />
                            <select id="user_id" name="user_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">— Assign to me (admin) —</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" @selected(old('user_id', $package->user_id) == $vendor->id)>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-label for="destination_id" value="Destination (optional)" />
                            <select id="destination_id" name="destination_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">— None —</option>
                                @foreach ($destinations as $destination)
                                    <option value="{{ $destination->id }}" @selected(old('destination_id', $package->destination_id) == $destination->id)>{{ $destination->name }} ({{ $destination->country }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-label for="type" value="Type" />
                            <select id="type" name="type" required class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">
                                @foreach (['hotel' => 'Hotel', 'tour' => 'Tour', 'destination' => 'Destination'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('type', $package->type ?? 'hotel') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-label for="home_section" value="Category" />
                            <select id="home_section" name="home_section" required class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">
                                @foreach (['default' => 'Default', 'popular' => 'Popular', 'trending' => 'Trending'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('home_section', $package->home_section ?? 'default') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-label for="status" value="Status" />
                            <select id="status" name="status" required class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">
                                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $package->status ?? 'approved') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-label for="property_type" value="Sub-type (e.g. resort, villa, guided tour)" />
                            <x-input id="property_type" name="property_type" class="block mt-1 w-full" :value="old('property_type', $package->property_type)" required />
                        </div>

                        <div>
                            <x-label for="city" value="City" />
                            <x-input id="city" name="city" class="block mt-1 w-full" :value="old('city', $package->city)" required />
                        </div>

                        <div>
                            <x-label for="country" value="Country" />
                            <x-input id="country" name="country" class="block mt-1 w-full" :value="old('country', $package->country)" required />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="address" value="Address (optional)" />
                            <x-input id="address" name="address" class="block mt-1 w-full" :value="old('address', $package->address)" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="description" value="Description" />
                            <textarea id="description" name="description" rows="6" required data-ckeditor class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">{{ old('description', $package->description) }}</textarea>
                        </div>

                        <div>
                            <x-label for="price_per_night" value="Price (per night / per person)" />
                            <x-input id="price_per_night" name="price_per_night" type="number" step="0.01" class="block mt-1 w-full" :value="old('price_per_night', $package->price_per_night)" required />
                        </div>

                        <div>
                            <x-label for="currency" value="Currency" />
                            <x-currency-select name="currency" :selected="old('currency', $package->currency ?? 'USD')" required />
                        </div>

                        <div>
                            <x-label for="max_guests" value="Max guests" />
                            <x-input id="max_guests" name="max_guests" type="number" class="block mt-1 w-full" :value="old('max_guests', $package->max_guests ?? 2)" required />
                        </div>

                        <div>
                            <x-label for="bedrooms" value="Bedrooms (optional)" />
                            <x-input id="bedrooms" name="bedrooms" type="number" class="block mt-1 w-full" :value="old('bedrooms', $package->bedrooms)" />
                        </div>

                        <div>
                            <x-label for="stars" value="Star rating (optional, 1-5)" />
                            <x-input id="stars" name="stars" type="number" min="1" max="5" class="block mt-1 w-full" :value="old('stars', $package->stars)" />
                        </div>

                        <div class="flex items-center gap-2 mt-6">
                            <x-checkbox id="is_featured" name="is_featured" value="1" :checked="old('is_featured', $package->is_featured)" />
                            <x-label for="is_featured" value="Feature on home page" />
                        </div>

                        <div>
                            <x-label for="image_files" value="Upload images (optional)" />
                            <input id="image_files" name="image_files[]" type="file" accept="image/*" multiple data-preview="#images-preview" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                        </div>

                        <div>
                            <x-label for="images" value="…or image URLs (comma separated, optional)" />
                            <x-input id="images" name="images" class="block mt-1 w-full" placeholder="https://example.com/1.jpg, https://example.com/2.jpg" />
                        </div>

                        <div class="sm:col-span-2">
                            <div id="images-preview" class="flex flex-wrap gap-2">
                                @if ($package->exists)
                                    @foreach ($package->images as $image)
                                        <img src="{{ str_starts_with($image, 'http') ? $image : \Illuminate\Support\Facades\Storage::disk('public')->url($image) }}" class="w-[200px] h-[140px] object-cover rounded-lg ring-1 ring-gray-900/10" alt="">
                                    @endforeach
                                @endif
                            </div>
                            @if ($package->exists && ! empty($package->images))
                                <p class="mt-1 text-xs text-gray-400">Current images &mdash; uploading new ones above will replace all of these.</p>
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="amenities" value="Amenities (comma separated)" />
                            <textarea id="amenities" name="amenities" rows="2" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full" placeholder="Free WiFi, Pool, Breakfast included">{{ old('amenities', is_array($package->amenities) ? implode(', ', $package->amenities) : '') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('dashboard.packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                        <x-button>{{ $package->exists ? 'Save changes' : 'Create package' }}</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
