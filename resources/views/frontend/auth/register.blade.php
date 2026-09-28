<x-guest-layout>
    <x-authentication-card title="Create your account" subtitle="Start booking your next stay in minutes.">
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-5" x-data="{ role: '{{ old('role', 'user') }}' }">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Jane Doe" />
            </div>

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            </div>

            <div>
                <x-label for="role" value="{{ __('I am registering as') }}" />
                <select id="role" name="role" x-model="role" required
                    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                    <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>Traveler &mdash; I want to book stays &amp; trips</option>
                    <option value="vendor" {{ old('role') === 'vendor' ? 'selected' : '' }}>Vendor &mdash; I want to list hotels, tours &amp; destinations</option>
                    <option value="rental_partner" {{ old('role') === 'rental_partner' ? 'selected' : '' }}>Rental Partner &mdash; I want to offer vehicles for hire</option>
                </select>
            </div>

            <div x-show="role === 'vendor' || role === 'rental_partner'" x-cloak class="space-y-5">
                <div class="border-t border-gray-200 pt-5">
                    <h3 class="text-sm font-semibold text-gray-700">Contact info</h3>
                </div>
                <div>
                    <x-label for="company_phone" value="{{ __('Contact phone') }}" />
                    <x-input id="company_phone" class="block mt-1 w-full" type="text" name="company_phone" :value="old('company_phone')" autocomplete="tel" placeholder="+1 555 000 0000" />
                </div>
                <div>
                    <x-label for="company_address" value="{{ __('Contact address') }}" />
                    <x-input id="company_address" class="block mt-1 w-full" type="text" name="company_address" :value="old('company_address')" autocomplete="street-address" placeholder="123 Main St, Springfield" />
                </div>

                <div class="border-t border-gray-200 pt-5">
                    <h3 class="text-sm font-semibold text-gray-700">Company details</h3>
                </div>
                <div>
                    <x-label for="company_name" value="{{ __('Company / Business name') }}" />
                    <x-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" autocomplete="organization" placeholder="Sunrise Travels Ltd." />
                </div>
                <div>
                    <x-label for="company_description" value="{{ __('Company description (optional)') }}" />
                    <textarea id="company_description" name="company_description" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Tell us about your business">{{ old('company_description') }}</textarea>
                </div>
                <div>
                    <x-label for="company_logo" value="{{ __('Company image / logo (optional)') }}" />
                    <input id="company_logo" type="file" name="company_logo" accept="image/*" class="block mt-1 w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-gray-200" />
                </div>

                <p class="text-xs text-gray-500">Vendor and rental partner accounts are reviewed by our team before you can publish listings or accept rides.</p>
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div>
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2 text-sm text-gray-600">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-indigo-600 hover:text-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-indigo-600 hover:text-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <x-button class="w-full justify-center py-2.5 text-sm normal-case tracking-normal">
                {{ __('Create account') }}
            </x-button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            {{ __('Already registered?') }}
            <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ __('Log in') }}</a>
        </p>
    </x-authentication-card>
</x-guest-layout>
