<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('New User') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white dark:bg-gray-800 p-6 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10">
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('dashboard.users.store') }}" class="space-y-5">
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

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('dashboard.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                        <x-button>Create user</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
