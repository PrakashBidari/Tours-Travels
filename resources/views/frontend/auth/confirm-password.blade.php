<x-guest-layout>
    <x-authentication-card title="Confirm your password" subtitle="This is a secure area of the application.">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Please confirm your password before continuing.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus />
            </div>

            <x-button class="w-full justify-center py-2.5 text-sm normal-case tracking-normal">
                {{ __('Confirm') }}
            </x-button>
        </form>
    </x-authentication-card>
</x-guest-layout>
