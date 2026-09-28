<x-site-layout :title="__('Refund & Cancellation Policy').' | '.site('name')" :breadcrumbs="[[__('Refund Policy'), null]]">
    <x-frontend.page-hero :title="__('Refund & Cancellation Policy')" :breadcrumbs="[[__('Refund Policy'), null]]" />
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
        <div class="rt-card rt-prose p-6 sm:p-10">
            <p><em>Last updated {{ now()->format('F Y') }}. This is a template — please have it reviewed before publishing.</em></p>
            <h2>1. Tour packages</h2>
            <ul>
                <li>Cancellation 30+ days before departure: full refund minus a 10% processing fee.</li>
                <li>15–29 days before departure: 50% refund.</li>
                <li>Less than 15 days / no-show: non-refundable.</li>
                <li>Peak-season and festival departures may carry stricter hotel and airline terms, which will be shared at booking.</li>
            </ul>
            <h2>2. Flight tickets</h2>
            <p>Refunds and date changes follow the fare rules of the issuing airline. Airline penalties and fare differences apply, plus our service charge. Submit a refund or reissue request with your PNR from the <a href="{{ route('flights.index') }}#services">Flight Booking</a> page.</p>
            <h2>3. Bus tickets</h2>
            <p>Bus tickets can be cancelled up to 12 hours before departure for a 75% refund. Later cancellations and no-shows are non-refundable.</p>
            <h2>4. Car rental</h2>
            <p>Free cancellation up to 48 hours before pickup. Within 48 hours, one day's rental is charged.</p>
            <h2>5. Visa services</h2>
            <p>Embassy fees are non-refundable once submitted. Our service charge is refundable only if we have not yet started processing your file. Visa approval is at the sole discretion of the embassy.</p>
            <h2>6. How refunds are paid</h2>
            <p>Approved refunds are returned to the original payment method (eSewa, Khalti, card, bank) within 7–14 working days.</p>
            <h2>7. Contact</h2>
            <p>{{ site('name') }}, {{ site('address') }} · {{ site('phone') }} · <a href="mailto:{{ site('email') }}">{{ site('email') }}</a></p>
        </div>
    </div>
</x-site-layout>
