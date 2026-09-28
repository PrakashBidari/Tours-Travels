<x-site-layout :title="__('Track Your Booking').' | '.site('name')" :breadcrumbs="[[__('Track Booking'), null]]">
    <x-frontend.page-hero :title="__('Track Your Booking')" :subtitle="__('Check status, pay online, download your invoice or print your ticket.')" :breadcrumbs="[[__('Track Booking'), null]]" />

    <div class="max-w-xl mx-auto px-4 sm:px-6 py-12">
        <form method="POST" action="{{ route('booking.track.lookup') }}" class="rt-card space-y-5 p-6 sm:p-8">
            @csrf
            <div>
                <label for="reference" class="rt-label">{{ __('Booking ID') }}</label>
                <input id="reference" name="reference" value="{{ old('reference') }}" required placeholder="RT260928ABCD" class="rt-input font-mono uppercase">
                <x-input-error for="reference" class="mt-1" />
            </div>
            <div>
                <label for="contact" class="rt-label">{{ __('Email or phone used for booking') }}</label>
                <input id="contact" name="contact" value="{{ old('contact') }}" required class="rt-input">
            </div>
            <button class="rt-btn-primary w-full"><x-icon name="search" class="h-4 w-4" /> {{ __('Find My Booking') }}</button>
            @auth
                <p class="text-center text-sm text-slate-500">{{ __('Signed in?') }} <a href="{{ Auth::user()->dashboardUrl() }}" class="font-semibold text-brand-700">{{ __('See all your bookings') }}</a></p>
            @endauth
        </form>
    </div>
</x-site-layout>
