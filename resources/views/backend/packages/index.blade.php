<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Packages') }}</h2>
            <a href="{{ route('dashboard.packages.create') }}" class="rounded-md bg-indigo-600 text-white text-sm font-medium px-4 py-2">+ New Package</a>
        </div>
    </x-slot>

    <div
        class="py-12"
        x-data="{
            categoryModalOpen: false,
            categoryPackageId: null,
            categoryPackageName: '',
            categoryValue: 'default',
            openCategoryModal(id, name, value) {
                this.categoryPackageId = id;
                this.categoryPackageName = name;
                this.categoryValue = value;
                this.categoryModalOpen = true;
            },
        }"
    >
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="flex gap-2">
                @foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                    <a href="{{ route('dashboard.packages.index', $value ? ['status' => $value] : []) }}"
                        class="px-3 py-1.5 rounded-full text-sm font-medium {{ request('status', '') === $value ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden">
                <div class="overflow-x-auto p-4">
                    <table data-datatable data-empty-message="No vendor packages found." class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Image</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Package</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Vendor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-filter data-filter-label="Type">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">City</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-filter data-filter-label="Status">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase" data-no-sort>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($packages as $package)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4"><img src="{{ $package->main_image }}" class="h-10 w-14 object-cover rounded" alt=""></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $package->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $package->vendor?->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 capitalize">{{ $package->type }}</td>
                                    <td class="px-6 py-4">
                                        <button
                                            type="button"
                                            @click="openCategoryModal({{ $package->id }}, {{ Js::from($package->name) }}, {{ Js::from($package->home_section) }})"
                                            @class([
                                                'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset hover:opacity-75 transition',
                                                'bg-gray-50 text-gray-600 ring-gray-500/20 dark:bg-gray-500/10 dark:text-gray-400' => $package->home_section === 'default',
                                                'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-400' => $package->home_section === 'popular',
                                                'bg-orange-50 text-orange-700 ring-orange-600/20 dark:bg-orange-500/10 dark:text-orange-400' => $package->home_section === 'trending',
                                            ])
                                        >{{ ucfirst($package->home_section) }}</button>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $package->city }}, {{ $package->country }}</td>
                                    <td class="px-6 py-4">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset',
                                            'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400' => $package->status === 'approved',
                                            'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400' => $package->status === 'pending',
                                            'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400' => $package->status === 'rejected',
                                        ])>{{ ucfirst($package->status) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        @if ($package->status !== 'approved')
                                            <form method="POST" action="{{ route('dashboard.packages.approve', $package) }}" class="inline">
                                                @csrf
                                                <button class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Approve</button>
                                            </form>
                                        @endif
                                        @if ($package->status !== 'rejected')
                                            <form method="POST" action="{{ route('dashboard.packages.reject', $package) }}" class="inline">
                                                @csrf
                                                <button class="text-xs font-medium text-red-600 hover:text-red-700">Reject</button>
                                            </form>
                                        @endif
                                        <a href="{{ route('dashboard.packages.edit', $package) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.packages.destroy', $package) }}" class="inline" data-confirm="This will permanently remove &quot;{{ $package->name }}&quot;. This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-medium text-red-600 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            x-show="categoryModalOpen"
            x-cloak
            x-trap.inert.noscroll="categoryModalOpen"
            @keydown.escape.window="categoryModalOpen = false"
            class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
        >
            <div class="fixed inset-0 bg-gray-500/75" @click="categoryModalOpen = false"></div>

            <div class="relative mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl sm:w-full sm:max-w-md sm:mx-auto">
                <form method="POST" :action="`/dashboard/packages/${categoryPackageId}/category`" class="p-6">
                    @csrf
                    @method('PATCH')

                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Change category</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="categoryPackageName"></p>

                    <div class="mt-4">
                        <x-label for="modal_home_section" value="Category" />
                        <select id="modal_home_section" name="home_section" x-model="categoryValue" required class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">
                            @foreach (['default' => 'Default', 'popular' => 'Popular', 'trending' => 'Trending'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="categoryModalOpen = false" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400">Cancel</button>
                        <x-button>Save</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
