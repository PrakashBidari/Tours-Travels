<x-account-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Your Details') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center gap-2 text-xs font-medium text-gray-400">
                <span class="text-brand-600">1. Your details</span>
                <span>&rarr;</span>
                <span>2. Confirm &amp; book</span>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($items as $item)
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['property']->name }}</p>
                            <p class="text-xs text-gray-500">{{ $item['check_in']->format('M d, Y') }} – {{ $item['check_out']->format('M d, Y') }} &middot; {{ $item['guests'] }} guest(s) &middot; {{ $item['nights'] }} night(s)</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 shrink-0">{{ $item['property']->currency }} {{ number_format($item['subtotal'], 2) }}</span>
                    </div>
                @endforeach
                <div class="px-6 py-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Total</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $items->first()['property']->currency }} {{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="rounded-xl bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100">Your contact details</h3>
                <p class="mt-1 text-sm text-gray-500">We ask for these on every booking so they're always current &mdash; feel free to update them below.</p>

                <form method="POST" action="{{ route('account.booking.details.update') }}" class="mt-5 space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="name" value="Full name" />
                            <x-input id="name" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" value="{{ $user->name }}" disabled />
                        </div>
                        <div>
                            <x-label for="email" value="Email" />
                            <x-input id="email" class="block mt-1 w-full bg-gray-100 dark:bg-gray-700" value="{{ $user->email }}" disabled />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="phone" value="Phone number" />
                            <x-input id="phone" name="phone" type="tel" class="block mt-1 w-full" :value="old('phone', $user->phone)" required placeholder="+1 555 000 0000" />
                        </div>
                        <div>
                            <x-label for="address" value="Address" />
                            <x-input id="address" name="address" class="block mt-1 w-full" :value="old('address', $user->address)" required placeholder="123 Main St" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-label for="city" value="City" />
                            <x-input id="city" name="city" class="block mt-1 w-full" :value="old('city', $user->city)" required />
                        </div>
                        <div>
                            <x-label for="country" value="Country" />
                            <x-input id="country" name="country" class="block mt-1 w-full" :value="old('country', $user->country)" required />
                        </div>
                        <div>
                            <x-label for="postal_code" value="Postal code (optional)" />
                            <x-input id="postal_code" name="postal_code" class="block mt-1 w-full" :value="old('postal_code', $user->postal_code)" />
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-button class="px-6 py-2.5">Continue to confirm</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-account-layout>
