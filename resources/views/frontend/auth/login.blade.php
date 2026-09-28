<x-guest-layout>
    <x-authentication-card title="Welcome back" subtitle="Log in to manage your bookings.">
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-500 font-medium" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-button class="w-full justify-center py-2.5 text-sm normal-case tracking-normal">
                {{ __('Log in') }}
            </x-button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Sign up') }}</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
