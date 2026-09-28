<x-guest-layout>
    <x-authentication-card title="Forgot your password?" subtitle="No problem. We'll email you a link to reset it.">
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            </div>

            <x-button class="w-full justify-center py-2.5 text-sm normal-case tracking-normal">
                {{ __('Email Password Reset Link') }}
            </x-button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">&larr; {{ __('Back to log in') }}</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
