<x-guest-layout>
    <x-authentication-card title="Verify your email" subtitle="One last step before you can start booking.">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </div>

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <x-button type="submit" class="py-2.5 text-sm normal-case tracking-normal">
                    {{ __('Resend Verification Email') }}
                </x-button>
            </form>

            <div class="flex items-center gap-4">
                <a
                    href="{{ route('profile.show') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                >
                    {{ __('Edit Profile') }}</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
