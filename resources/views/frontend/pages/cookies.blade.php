<x-site-layout :title="__('Cookie Policy').' | '.site('name')" :breadcrumbs="[[__('Cookie Policy'), null]]">
    <x-frontend.page-hero :title="__('Cookie Policy')" :breadcrumbs="[[__('Cookie Policy'), null]]" />
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
        <div class="rt-card rt-prose p-6 sm:p-10">
            <p><em>Last updated {{ now()->format('F Y') }}. This is a template — please have it reviewed before publishing.</em></p>
            <h2>What are cookies?</h2>
            <p>Cookies are small text files stored on your device that help websites remember information about your visit.</p>
            <h2>Cookies we use</h2>
            <ul>
                <li><strong>Essential:</strong> session and security (CSRF) cookies needed to sign in and submit bookings.</li>
                <li><strong>Preferences:</strong> your language and currency choice.</li>
                <li><strong>Local storage:</strong> your wishlist, compare list and recently viewed packages are stored only in your browser.</li>
                <li><strong>Analytics & marketing:</strong> if enabled, Google Analytics and Facebook Pixel help us understand how the site is used.</li>
            </ul>
            <h2>Managing cookies</h2>
            <p>You can block or delete cookies in your browser settings. Blocking essential cookies may stop bookings and sign-in from working.</p>
            <h2>Contact</h2>
            <p><a href="mailto:{{ site('email') }}">{{ site('email') }}</a></p>
        </div>
    </div>
</x-site-layout>
