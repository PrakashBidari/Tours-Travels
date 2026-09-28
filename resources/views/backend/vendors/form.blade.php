<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('New Vendor') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-validation-errors class="mb-4" />

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    Requires the same details a vendor gives when self-registering. The account is created and
                    auto-approved immediately &mdash; no separate approval step is needed.
                </p>

                <form method="POST" action="{{ route('dashboard.vendors.store') }}" class="space-y-5" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-label for="name" value="Name" />
                            <x-input id="name" name="name" class="block mt-1 w-full" :value="old('name')" required autofocus />
                        </div>

                        <div>
                            <x-label for="email" value="Email" />
                            <x-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" required />
                        </div>

                        <div>
                            <x-label for="password" value="Password" />
                            <x-input id="password" name="password" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
                        </div>

                        <div>
                            <x-label for="password_confirmation" value="Confirm password" />
                            <x-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Contact info</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-label for="company_phone" value="Contact phone" />
                            <x-input id="company_phone" name="company_phone" class="block mt-1 w-full" :value="old('company_phone')" required />
                        </div>

                        <div>
                            <x-label for="company_address" value="Contact address" />
                            <x-input id="company_address" name="company_address" class="block mt-1 w-full" :value="old('company_address')" required />
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Company details</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="sm:col-span-2">
                            <x-label for="company_name" value="Company / Business name" />
                            <x-input id="company_name" name="company_name" class="block mt-1 w-full" :value="old('company_name')" required />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="company_description" value="Company description (optional)" />
                            <textarea id="company_description" name="company_description" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm block mt-1 w-full">{{ old('company_description') }}</textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="company_logo" value="Company image / logo (optional)" />
                            <input id="company_logo" name="company_logo" type="file" accept="image/*" data-preview="#logo-preview" class="block mt-1 w-full text-sm text-gray-600 dark:text-gray-300">
                            <div id="logo-preview" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('dashboard.vendors.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                        <x-button>Create vendor</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
