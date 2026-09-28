<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Page Settings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ socialModalOpen: false }">
            <x-validation-errors class="mb-4" />

            <!-- About / Vision / Mission / Contact info -->
            <form method="POST" action="{{ route('dashboard.page-settings.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <x-admin.card title="Company & contact" description="Shown in the top bar, footer, contact page, invoices and emails.">
                    <x-admin.field name="company_name" label="Company name" :value="$settings->company_name" :placeholder="config('travel.company.name')" />
                    <x-admin.field name="whatsapp_number" label="WhatsApp number (with country code)" :value="$settings->whatsapp_number" placeholder="9779801234567" />
                    <x-admin.field name="contact_mobile" label="Mobile" :value="$settings->contact_mobile" />
                    <x-admin.field name="office_hours" label="Office hours" :value="$settings->office_hours" />
                    <x-admin.field name="map_embed_url" label="Google Maps embed URL" type="url" :value="$settings->map_embed_url" full help="Google Maps → Share → Embed a map → copy the src URL." />
                </x-admin.card>

                <x-admin.card title="Homepage hero" description="Leave blank to use the default text from the design.">
                    <x-admin.field name="hero_script" label="Script line" :value="$settings->hero_script" placeholder="Explore the World" />
                    <x-admin.field name="hero_title" label="Headline" :value="$settings->hero_title" placeholder="Discover New Destinations" />
                    <x-admin.field name="hero_subtitle" label="Subtitle" type="textarea" rows="2" :value="$settings->hero_subtitle" />
                    <x-admin.field name="hero_video_url" label="Background video URL (.mp4, optional)" type="url" :value="$settings->hero_video_url" full />
                    <x-admin.field name="hero_image" label="Background image" type="image" :value="$settings->hero_image" full />
                </x-admin.card>

                <x-admin.card title="Why choose us & stats">
                    <x-admin.field name="why_title" label="Title" :value="$settings->why_title" full />
                    <x-admin.field name="why_description" label="Text" type="textarea" rows="3" :value="$settings->why_description" />
                    <x-admin.field name="stat_years" label="Years of experience" type="number" min="0" :value="$settings->stat_years" />
                    <x-admin.field name="stat_travelers" label="Happy travelers" type="number" min="0" :value="$settings->stat_travelers" />
                    <x-admin.field name="stat_destinations" label="Destinations" type="number" min="0" :value="$settings->stat_destinations" />
                    <x-admin.field name="why_image" label="Image" type="image" :value="$settings->why_image" full />
                </x-admin.card>

                <x-admin.card title="SEO & tracking">
                    <x-admin.field name="meta_title" label="Default meta title" :value="$settings->meta_title" full />
                    <x-admin.field name="meta_description" label="Default meta description" type="textarea" rows="2" :value="$settings->meta_description" />
                    <x-admin.field name="google_analytics_id" label="Google Analytics ID" :value="$settings->google_analytics_id" placeholder="G-XXXXXXXXXX" />
                    <x-admin.field name="facebook_pixel_id" label="Facebook Pixel ID" :value="$settings->facebook_pixel_id" />
                </x-admin.card>

                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">About Us &mdash; intro section</h3>
                    <p class="mt-1 text-sm text-gray-500">Two-column image + description shown near the top of the About Us page.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-label for="about_title" value="Title" />
                            <x-input id="about_title" name="about_title" class="block mt-1 w-full" :value="old('about_title', $settings->about_title)" placeholder="About Us" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="about_description" value="Description" />
                            <textarea id="about_description" name="about_description" rows="6" data-ckeditor class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">{{ old('about_description', $settings->about_description) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="about_image" value="Image" />
                            <input id="about_image" name="about_image" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                            @if ($settings->about_image_url)
                                <img src="{{ $settings->about_image_url }}" class="mt-2 w-[220px] h-[140px] object-cover rounded-lg ring-1 ring-gray-900/10" alt="">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Our Vision</h3>
                    <p class="mt-1 text-sm text-gray-500">Two-column image + description on the About Us page.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-label for="vision_title" value="Title" />
                            <x-input id="vision_title" name="vision_title" class="block mt-1 w-full" :value="old('vision_title', $settings->vision_title)" placeholder="Our Vision" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="vision_description" value="Description" />
                            <textarea id="vision_description" name="vision_description" rows="5" data-ckeditor class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">{{ old('vision_description', $settings->vision_description) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="vision_image" value="Image" />
                            <input id="vision_image" name="vision_image" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                            @if ($settings->vision_image_url)
                                <img src="{{ $settings->vision_image_url }}" class="mt-2 w-[220px] h-[140px] object-cover rounded-lg ring-1 ring-gray-900/10" alt="">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Our Mission</h3>
                    <p class="mt-1 text-sm text-gray-500">Two-column image + description on the About Us page.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-label for="mission_title" value="Title" />
                            <x-input id="mission_title" name="mission_title" class="block mt-1 w-full" :value="old('mission_title', $settings->mission_title)" placeholder="Our Mission" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="mission_description" value="Description" />
                            <textarea id="mission_description" name="mission_description" rows="5" data-ckeditor class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">{{ old('mission_description', $settings->mission_description) }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="mission_image" value="Image" />
                            <input id="mission_image" name="mission_image" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                            @if ($settings->mission_image_url)
                                <img src="{{ $settings->mission_image_url }}" class="mt-2 w-[220px] h-[140px] object-cover rounded-lg ring-1 ring-gray-900/10" alt="">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">Contact Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Shown on the Contact Us page and used for the map.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-label for="contact_email" value="Email" />
                            <x-input id="contact_email" name="contact_email" type="email" class="block mt-1 w-full" :value="old('contact_email', $settings->contact_email)" />
                        </div>

                        <div>
                            <x-label for="contact_phone" value="Phone" />
                            <x-input id="contact_phone" name="contact_phone" class="block mt-1 w-full" :value="old('contact_phone', $settings->contact_phone)" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="contact_address" value="Address" />
                            <x-input id="contact_address" name="contact_address" class="block mt-1 w-full" :value="old('contact_address', $settings->contact_address)" />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="footer_tagline" value="Footer tagline" />
                            <x-input id="footer_tagline" name="footer_tagline" class="block mt-1 w-full" :value="old('footer_tagline', $settings->footer_tagline)" placeholder="Find and book stays worldwide — hotels, apartments, resorts and more." />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="footer_icon" value="Footer icon" />
                            <input id="footer_icon" name="footer_icon" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                            <p class="mt-1 text-xs text-gray-400">Replaces the default building icon next to the site name in the footer.</p>
                            @if ($settings->footer_icon_url)
                                <img src="{{ $settings->footer_icon_url }}" class="mt-2 h-10 w-10 object-cover rounded-lg ring-1 ring-gray-900/10" alt="">
                            @endif
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="copyright_text" value="Copyright text" />
                            <textarea id="copyright_text" name="copyright_text" rows="2" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full font-mono text-sm" placeholder="&amp;copy; {{ '{year}' }} {{ config('app.name', 'Booking') }}. All rights reserved.">{{ old('copyright_text', $settings->copyright_text) }}</textarea>
                            <p class="mt-1 text-xs text-gray-400">Shown at the bottom of the footer. Use <code>{{ '{year}' }}</code> for the current year, and HTML like <code>&lt;a href="/about"&gt;About&lt;/a&gt;</code> to add a link. Leave blank to use the default.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-button>Save changes</x-button>
                </div>
            </form>

            <!-- Social icons -->
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Social Icons</h3>
                        <p class="mt-1 text-sm text-gray-500">Shown in the site footer under "Follow us".</p>
                    </div>
                    <x-button type="button" @click="socialModalOpen = true">Manage social icons</x-button>
                </div>

                <div class="mt-4 flex flex-wrap gap-3">
                    @forelse ($socialLinks as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300" title="{{ $link->label }}">
                            <x-social-icon :platform="$link->platform" class="h-4 w-4" />
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">No social icons configured yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Footer links -->
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Footer Links</h3>
                <p class="mt-1 text-sm text-gray-500">Manage the link columns shown in the site footer.</p>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($footerColumns as $column)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('dashboard.page-settings.footer-columns.update', $column) }}" class="flex-1 flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="heading" value="{{ $column->heading }}" class="flex-1 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm font-semibold">
                                    <button class="text-xs font-medium text-indigo-600 hover:text-indigo-700 shrink-0">Save</button>
                                </form>
                                <form method="POST" action="{{ route('dashboard.page-settings.footer-columns.destroy', $column) }}" data-confirm="Delete this column and all its links?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs font-medium text-red-600 hover:text-red-700 shrink-0">Delete</button>
                                </form>
                            </div>

                            <div class="mt-3 space-y-2">
                                @foreach ($column->links as $link)
                                    <div class="flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('dashboard.page-settings.footer-links.update', $link) }}" class="flex-1 flex items-center gap-1.5 min-w-0">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="label" value="{{ $link->label }}" placeholder="Label" class="w-1/2 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                            <input type="text" name="url" value="{{ $link->url }}" placeholder="URL" class="w-1/2 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                            <button class="text-xs font-medium text-indigo-600 hover:text-indigo-700 shrink-0">Save</button>
                                        </form>
                                        <form method="POST" action="{{ route('dashboard.page-settings.footer-links.destroy', $link) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-medium text-red-600 hover:text-red-700 shrink-0" aria-label="Delete link">&times;</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>

                            <form method="POST" action="{{ route('dashboard.page-settings.footer-links.store') }}" class="mt-3 flex items-center gap-1.5">
                                @csrf
                                <input type="hidden" name="footer_column_id" value="{{ $column->id }}">
                                <input type="text" name="label" placeholder="New link label" class="w-1/2 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                <input type="text" name="url" placeholder="URL" class="w-1/2 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                <button class="text-xs font-medium text-indigo-600 hover:text-indigo-700 shrink-0">+ Add</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('dashboard.page-settings.footer-columns.store') }}" class="mt-4 flex items-center gap-2">
                    @csrf
                    <input type="text" name="heading" required placeholder="New column heading (e.g. Company)" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm w-72">
                    <button class="text-sm font-medium text-indigo-600 hover:text-indigo-700">+ Add column</button>
                </form>
            </div>

            <!-- Social icons modal -->
            <div
                x-show="socialModalOpen"
                x-cloak
                x-trap.inert.noscroll="socialModalOpen"
                @keydown.escape.window="socialModalOpen = false"
                class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
            >
                <div class="fixed inset-0 bg-gray-500/75" @click="socialModalOpen = false"></div>

                <div class="relative mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl sm:w-full sm:max-w-lg sm:mx-auto">
                    <form method="POST" action="{{ route('dashboard.page-settings.social-links.update') }}" class="p-6">
                        @csrf
                        @method('PUT')

                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Social icons</h3>
                        <p class="mt-1 text-sm text-gray-500">Add a link for any platform you want to show in the footer. Leave blank to hide it.</p>

                        <div class="mt-4 space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                            @foreach ($platforms as $platform => $label)
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        <x-social-icon :platform="$platform" class="h-4 w-4" />
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</label>
                                        <input
                                            type="url"
                                            name="social[{{ $platform }}]"
                                            value="{{ old('social.'.$platform, $socialLinks[$platform]->url ?? '') }}"
                                            placeholder="https://..."
                                            class="mt-0.5 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm"
                                        >
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button type="button" @click="socialModalOpen = false" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                            <x-button>Save</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
