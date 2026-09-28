<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">Header Settings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-validation-errors class="mb-4" />

            <!-- General: logo + background color -->
            <form method="POST" action="{{ route('dashboard.header-settings.general.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">General</h3>
                    <p class="mt-1 text-sm text-gray-500">Logo and background color shown in the public site header.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-label for="logo" value="Logo" />
                            <input id="logo" name="logo" type="file" accept="image/*" data-preview="#logo-preview" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                            <p class="mt-1 text-xs text-gray-400">Replaces the default icon + site name in the header. Leave blank to keep the current logo.</p>
                            <div id="logo-preview" class="mt-2">
                                @if ($settings->logo_url)
                                    <img src="{{ $settings->logo_url }}" class="h-12 w-auto object-contain rounded-lg bg-gray-100 dark:bg-gray-900 p-1.5 ring-1 ring-gray-900/10" alt="">
                                @endif
                            </div>
                        </div>

                        <div x-data="{ color: '{{ old('background_color', $settings->background_color ?? '#166b68') }}' }">
                            <x-label for="background_color_text" value="Background color" />
                            <div class="mt-1 flex items-center gap-2">
                                <input type="color" x-model="color" @input="$refs.hex.value = color" class="h-9 w-12 shrink-0 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-0.5">
                                <input id="background_color_text" x-ref="hex" type="text" name="background_color" x-model="color" placeholder="#166b68" pattern="^#[0-9A-Fa-f]{6}$" class="flex-1 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm font-mono">
                                <button type="button" @click="color = '#166b68'; $refs.hex.value = color" class="shrink-0 text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400">Reset</button>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">Hex color applied to the header background.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-button>Save changes</x-button>
                    </div>
                </div>
            </form>

            <!-- Navigation menu -->
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Navigation Menu</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Drag items (using the
                    <svg xmlns="http://www.w3.org/2000/svg" class="inline h-4 w-4 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6h16.5" /></svg>
                    handle) to reorder or nest them as submenus, or use the indent/outdent buttons. Changes save automatically.
                </p>

                <form method="POST" action="{{ route('dashboard.header-settings.menu-items.store') }}" class="mt-4 flex flex-wrap items-center gap-1.5">
                    @csrf
                    <input type="text" name="label" required placeholder="Label" class="w-40 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm">
                    <input type="text" name="url" required placeholder="URL (e.g. /destinations)" class="flex-1 min-w-[180px] border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm">
                    <select name="target" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm">
                        <option value="_self">Same tab</option>
                        <option value="_blank">New tab</option>
                    </select>
                    <button class="text-sm font-medium text-indigo-600 hover:text-indigo-700">+ Add menu item</button>
                </form>

                <div data-nav-menu-builder data-reorder-url="{{ route('dashboard.header-settings.menu-items.reorder') }}" class="mt-4">
                    <div id="navMenuSortable" class="menu-sortable-list space-y-2" data-parent="0">
                        @forelse ($menuItems as $item)
                            @include('backend.header-settings.partials.menu-item', ['item' => $item])
                        @empty
                            <p id="emptyState" class="text-sm text-gray-400 py-4 text-center">No menu items yet &mdash; add one above.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Header buttons -->
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Header Buttons</h3>
                <p class="mt-1 text-sm text-gray-500">Custom buttons shown on the right side of the header, alongside the automatic Sign in / Register / Dashboard buttons. Drag to reorder.</p>

                <form method="POST" action="{{ route('dashboard.header-settings.buttons.store') }}" class="mt-4 flex flex-wrap items-center gap-1.5">
                    @csrf
                    <input type="text" name="label" required placeholder="Label" class="w-40 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm">
                    <input type="text" name="url" required placeholder="URL" class="flex-1 min-w-[160px] border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm">
                    <select name="style" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm">
                        <option value="solid">Solid</option>
                        <option value="outline">Outline</option>
                    </select>
                    <select name="target" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded-md shadow-sm text-sm">
                        <option value="_self">Same tab</option>
                        <option value="_blank">New tab</option>
                    </select>
                    <button class="text-sm font-medium text-indigo-600 hover:text-indigo-700">+ Add button</button>
                </form>

                <ul id="header-buttons-list" data-sortable-list data-sortable-url="{{ route('dashboard.header-settings.buttons.reorder') }}" class="mt-4 space-y-2">
                    @forelse ($buttons as $button)
                        <li data-id="{{ $button->id }}" draggable="true" class="relative flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 cursor-move select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6h16.5" />
                            </svg>

                            <form method="POST" action="{{ route('dashboard.header-settings.buttons.update', $button) }}" class="flex flex-1 flex-wrap items-center gap-1.5 min-w-0" draggable="false">
                                @csrf
                                @method('PUT')
                                <input type="text" name="label" value="{{ $button->label }}" placeholder="Label" class="w-32 min-w-0 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                <input type="text" name="url" value="{{ $button->url }}" placeholder="URL" class="flex-1 min-w-[140px] border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                <select name="style" class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                    <option value="solid" @selected($button->style === 'solid')>Solid</option>
                                    <option value="outline" @selected($button->style === 'outline')>Outline</option>
                                </select>
                                <select name="target" class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 rounded-md shadow-sm text-xs">
                                    <option value="_self" @selected($button->target === '_self')>Same tab</option>
                                    <option value="_blank" @selected($button->target === '_blank')>New tab</option>
                                </select>
                                <button type="submit" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 shrink-0">Save</button>
                            </form>

                            <form method="POST" action="{{ route('dashboard.header-settings.buttons.destroy', $button) }}" class="shrink-0" draggable="false">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                            </form>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 py-4 text-center">No custom header buttons yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <style>
        .item-children {
            border-left: 2px solid #e0e7ff;
            margin-left: 18px;
            padding-left: 14px;
            margin-top: 0.5rem;
        }
        .dark .item-children {
            border-left-color: #3730a3;
        }
        .item-children:empty {
            display: none;
        }
        .sortable-ghost {
            opacity: 0.4;
        }
        .sortable-chosen .menu-item-row {
            box-shadow: 0 0 0 2px #6366f1;
        }
    </style>
</x-app-layout>
