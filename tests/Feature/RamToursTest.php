<?php

namespace Tests\Feature;

use App\Models\BusRoute;
use App\Models\Inquiry;
use App\Models\Offer;
use App\Models\ServiceBooking;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VisaService;
use App\Services\BookingService;
use Database\Seeders\RamToursSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RamToursTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        $this->admin = User::factory()->create(['role' => 'super_admin', 'email_verified_at' => now()]);
        $this->seed(RamToursSeeder::class);
    }

    protected function traveler(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Sita Sharma',
            'email' => 'sita@example.com',
            'phone' => '9801234567',
            'nationality' => 'Nepali',
        ], $overrides);
    }

    public function test_public_pages_render(): void
    {
        $tour = TourPackage::first();
        $vehicle = Vehicle::first();
        $visa = VisaService::first();
        $route = BusRoute::first();

        foreach ([
            '/', '/tours', '/tours?category=trekking&duration=8-14&sort=price_asc', route('tours.show', $tour), route('tours.compare', ['tours' => $tour->slug]),
            '/flights', '/bus-tickets', '/bus-tickets/search?from=Kathmandu&to=Pokhara', route('bus.seats', $route),
            '/car-rental', route('cars.show', $vehicle), '/visa', route('visa.show', $visa), '/offers', '/blog', '/gallery',
            '/reviews', '/faq', '/track-booking', '/refund-policy', '/cookie-policy', '/sitemap.xml', '/about', '/contact',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_itinerary_pdf_downloads(): void
    {
        $this->get(route('tours.itinerary', TourPackage::first()))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_tour_booking_with_coupon_charges_children_half(): void
    {
        $tour = TourPackage::where('category', 'nepal')->whereNull('sale_price')->first();
        $offer = Offer::create(['title' => 'Test', 'code' => 'TEST10', 'discount_type' => 'percent', 'discount_value' => 10, 'applies_to' => 'tour', 'is_active' => true]);

        $response = $this->post(route('tours.book', $tour), $this->traveler([
            'travel_date' => now()->addWeek()->toDateString(),
            'adults' => 2,
            'children' => 1,
            'coupon_code' => 'test10',
        ]));

        $booking = ServiceBooking::firstOrFail();
        $response->assertRedirect();
        $this->assertSame('tour', $booking->service_type);
        $this->assertEquals($tour->price * 2.5, (float) $booking->subtotal);
        $this->assertEquals(round($tour->price * 2.5 * 0.1, 2), (float) $booking->discount);
        $this->assertSame(1, $offer->fresh()->used_count);
    }

    public function test_invalid_coupon_is_rejected(): void
    {
        $this->post(route('tours.book', TourPackage::first()), $this->traveler([
            'travel_date' => now()->addWeek()->toDateString(), 'adults' => 1, 'coupon_code' => 'NOPE',
        ]))->assertSessionHasErrors('coupon_code');

        $this->assertDatabaseCount('service_bookings', 0);
    }

    public function test_coupon_preview_endpoint(): void
    {
        $this->postJson(route('coupons.check'), ['code' => 'DASHAIN15', 'amount' => 20000, 'service' => 'tour'])
            ->assertOk()->assertJson(['valid' => true, 'discount' => 3000]);

        $this->postJson(route('coupons.check'), ['code' => 'DASHAIN15', 'amount' => 20000, 'service' => 'bus'])
            ->assertStatus(422)->assertJson(['valid' => false]);
    }

    public function test_bus_seats_cannot_be_double_booked(): void
    {
        $route = BusRoute::first();
        $date = now()->addDays(3)->toDateString();

        $this->post(route('bus.book', $route), $this->traveler(['travel_date' => $date, 'seats' => ['1A', '1B']]))->assertRedirect();
        $this->assertSame(['1A', '1B'], $route->bookedSeats($date));
        $this->assertEquals($route->price * 2, (float) ServiceBooking::first()->total);

        $this->post(route('bus.book', $route), $this->traveler(['travel_date' => $date, 'seats' => ['1B', '2A']]))
            ->assertSessionHasErrors('seats');
        $this->assertDatabaseCount('service_bookings', 1);
    }

    public function test_cancelling_a_bus_booking_releases_seats(): void
    {
        $route = BusRoute::first();
        $date = now()->addDays(3)->toDateString();
        $this->post(route('bus.book', $route), $this->traveler(['travel_date' => $date, 'seats' => ['3C']]));
        $booking = ServiceBooking::first();

        $this->actingAs($this->admin)->put(route('dashboard.service-bookings.update', $booking), [
            'status' => 'cancelled', 'payment_status' => 'unpaid',
        ])->assertRedirect();

        $this->assertSame([], $route->bookedSeats($date));
    }

    public function test_car_booking_prices_driver_days_and_blocks_overlaps(): void
    {
        $vehicle = Vehicle::where('self_drive', false)->first();
        $from = now()->addDays(5);

        $payload = $this->traveler([
            'pickup_location' => 'Tribhuvan Airport', 'pickup_date' => $from->toDateString(), 'return_date' => $from->copy()->addDays(2)->toDateString(),
            'pickup_time' => '09:00', 'drive_mode' => 'driver', 'passengers' => 2,
        ]);

        $this->post(route('cars.book', $vehicle), $payload)->assertRedirect();
        $booking = ServiceBooking::first();
        $this->assertSame(3, $booking->quantity);
        $this->assertEquals(($vehicle->price_per_day + $vehicle->driver_charge_per_day) * 3, (float) $booking->subtotal);

        $this->post(route('cars.book', $vehicle), $payload)->assertSessionHasErrors('pickup_date');
        $this->post(route('cars.book', $vehicle), array_merge($payload, ['drive_mode' => 'self', 'pickup_date' => now()->addMonth()->toDateString(), 'return_date' => now()->addMonth()->toDateString()]))
            ->assertSessionHasErrors('drive_mode');
    }

    public function test_visa_application_stores_documents_privately(): void
    {
        Storage::fake('local');
        $visa = VisaService::first();

        $this->post(route('visa.apply', $visa), $this->traveler([
            'passport_number' => 'PA1234567', 'applicants' => 2,
            'documents' => [UploadedFile::fake()->create('passport.pdf', 200, 'application/pdf')],
        ]))->assertRedirect();

        $booking = ServiceBooking::first();
        $this->assertEquals($visa->total_fee * 2, (float) $booking->subtotal);
        Storage::disk('local')->assertExists($booking->details['documents'][0]['path']);

        $this->actingAs($this->admin)->get(route('dashboard.service-bookings.document', [$booking, 0]))->assertOk();
    }

    public function test_flight_request_is_saved_for_quoting(): void
    {
        $this->post(route('flights.book'), $this->traveler([
            'trip_type' => 'round', 'from' => 'KTM', 'to' => 'DXB', 'depart' => now()->addWeek()->toDateString(),
            'return' => now()->addWeeks(2)->toDateString(), 'adults' => 2, 'cabin' => 'economy',
        ]))->assertRedirect();

        $booking = ServiceBooking::first();
        $this->assertSame('flight', $booking->service_type);
        $this->assertEquals(0, (float) $booking->total);
        $this->assertFalse($booking->isPayable());
    }

    public function test_pnr_inquiry_is_recorded(): void
    {
        $this->post(route('flights.inquiry'), ['type' => 'pnr', 'name' => 'Ram', 'email' => 'ram@example.com', 'phone' => '980000000', 'pnr' => 'ABC123'])
            ->assertRedirect()->assertSessionHas('status');

        $this->assertSame('ABC123', Inquiry::first()->data['pnr']);
    }

    public function test_confirmation_pages_require_a_signed_link(): void
    {
        $this->post(route('tours.book', TourPackage::first()), $this->traveler(['travel_date' => now()->addWeek()->toDateString(), 'adults' => 1]));
        $booking = ServiceBooking::first();

        $this->get(route('booking.show', $booking))->assertForbidden();
        $this->get(BookingService::confirmationUrl($booking))->assertOk()->assertSee($booking->reference);
        $this->get(URL::signedRoute('booking.invoice', $booking))->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->get(URL::signedRoute('booking.pay', $booking))->assertOk()->assertSee('eSewa');
    }

    public function test_booking_tracking_matches_email_or_phone(): void
    {
        $this->post(route('tours.book', TourPackage::first()), $this->traveler(['travel_date' => now()->addWeek()->toDateString(), 'adults' => 1]));
        $booking = ServiceBooking::first();

        $this->post(route('booking.track.lookup'), ['reference' => strtolower($booking->reference), 'contact' => 'SITA@example.com'])
            ->assertRedirectContains('/booking/'.$booking->reference);
        $this->post(route('booking.track.lookup'), ['reference' => $booking->reference, 'contact' => '+977 980-1234567'])
            ->assertRedirectContains('/booking/'.$booking->reference);
        $this->post(route('booking.track.lookup'), ['reference' => $booking->reference, 'contact' => 'someone@else.com'])
            ->assertSessionHasErrors('reference');
    }

    public function test_esewa_payment_is_verified_before_marking_paid(): void
    {
        $this->post(route('tours.book', TourPackage::first()), $this->traveler(['travel_date' => now()->addWeek()->toDateString(), 'adults' => 1]));
        $booking = ServiceBooking::first();
        $total = number_format((float) $booking->total, 2, '.', '');
        $uuid = $booking->reference.'-101010';

        $sign = fn (array $p) => base64_encode(hash_hmac('sha256', "transaction_code={$p['transaction_code']},status={$p['status']},total_amount={$p['total_amount']},transaction_uuid={$p['transaction_uuid']},product_code={$p['product_code']},signed_field_names={$p['signed_field_names']}", config('travel.esewa.secret_key'), true));
        $payload = ['transaction_code' => '000AWEO', 'status' => 'COMPLETE', 'total_amount' => $total, 'transaction_uuid' => $uuid, 'product_code' => 'EPAYTEST', 'signed_field_names' => 'transaction_code,status,total_amount,transaction_uuid,product_code,signed_field_names'];

        Http::fake(['*' => Http::response(['status' => 'COMPLETE'])]);

        // Tampered signature → not paid, even though the status API says COMPLETE.
        $this->get(route('payment.esewa.success', ['data' => base64_encode(json_encode($payload + ['signature' => 'forged']))]));
        $this->assertFalse($booking->fresh()->isPaid());

        $this->get(route('payment.esewa.success', ['data' => base64_encode(json_encode($payload + ['signature' => $sign($payload)]))]))->assertRedirect();

        $booking->refresh();
        $this->assertTrue($booking->isPaid());
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('000AWEO', $booking->transaction_id);
    }

    public function test_admin_pages_render_for_super_admin(): void
    {
        $this->post(route('bus.book', BusRoute::first()), $this->traveler(['travel_date' => now()->addDay()->toDateString(), 'seats' => ['1A']]));
        $booking = ServiceBooking::first();
        Inquiry::create(['type' => 'quote', 'name' => 'A', 'email' => 'a@example.com', 'message' => 'Hi']);

        $this->actingAs($this->admin);

        foreach ([
            route('dashboard'), route('dashboard.service-bookings.index', ['service' => 'bus', 'q' => 'Sita']), route('dashboard.service-bookings.show', $booking),
            route('dashboard.service-bookings.export'), route('dashboard.tour-packages.index'), route('dashboard.tour-packages.create'),
            route('dashboard.tour-packages.edit', TourPackage::first()), route('dashboard.vehicles.index'), route('dashboard.vehicles.edit', Vehicle::first()),
            route('dashboard.bus-routes.index'), route('dashboard.bus-routes.create'), route('dashboard.bus-routes.seats', BusRoute::first()),
            route('dashboard.visa-services.index'), route('dashboard.visa-services.edit', VisaService::first()), route('dashboard.inquiries.index'),
            route('dashboard.inquiries.show', Inquiry::first()), route('dashboard.customers.index'), route('dashboard.customers.show', 'sita@example.com'),
            route('dashboard.offers.index'), route('dashboard.offers.create'), route('dashboard.testimonials.index'), route('dashboard.testimonials.create'),
            route('dashboard.subscribers.index'), route('dashboard.blog-posts.index'), route('dashboard.blog-posts.create'), route('dashboard.faqs.index'),
            route('dashboard.faqs.create'), route('dashboard.gallery.index'), route('dashboard.staff.index'), route('dashboard.staff.create'),
            route('dashboard.page-settings.index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_can_create_a_tour_package(): void
    {
        $this->actingAs($this->admin)->post(route('dashboard.tour-packages.store'), [
            'title' => 'Nagarkot Sunrise Escape', 'category' => 'nepal', 'destination' => 'Nagarkot', 'country' => 'Nepal',
            'duration_days' => 2, 'duration_nights' => 1, 'price' => 9500, 'season' => 'all', 'is_active' => '1', 'is_featured' => '0',
            'itinerary' => "Drive to Nagarkot | Sunset view\nSunrise | Return to Kathmandu",
            'includes' => "Hotel\nBreakfast", 'images_list' => 'https://example.com/a.jpg',
        ])->assertRedirect(route('dashboard.tour-packages.index'));

        $tour = TourPackage::where('slug', 'nagarkot-sunrise-escape')->firstOrFail();
        $this->assertSame('Sunrise', $tour->itinerary[1]['title']);
        $this->assertSame(['Hotel', 'Breakfast'], $tour->includes);
        $this->get(route('tours.show', $tour))->assertOk()->assertSee('Nagarkot Sunrise Escape');
    }

    public function test_staff_permissions_follow_their_role(): void
    {
        $editor = User::factory()->create(['role' => 'staff', 'staff_role' => 'editor', 'email_verified_at' => now()]);
        $ticketing = User::factory()->create(['role' => 'staff', 'staff_role' => 'ticketing', 'email_verified_at' => now()]);

        $this->actingAs($editor)->get(route('dashboard'))->assertOk();
        $this->actingAs($editor)->get(route('dashboard.blog-posts.index'))->assertOk();
        $this->actingAs($editor)->get(route('dashboard.service-bookings.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('dashboard.vendors.index'))->assertForbidden();

        $this->actingAs($ticketing)->get(route('dashboard.service-bookings.index'))->assertOk();
        $this->actingAs($ticketing)->get(route('dashboard.tour-packages.index'))->assertForbidden();
        $this->actingAs($ticketing)->get(route('dashboard.staff.index'))->assertForbidden();

        $this->assertSame(route('dashboard'), $editor->dashboardUrl());
    }

    public function test_admin_can_create_staff_member(): void
    {
        $this->actingAs($this->admin)->post(route('dashboard.staff.store'), [
            'name' => 'Hari', 'email' => 'hari@ramtours.com.np', 'staff_role' => 'finance',
            'password' => 'Password123!', 'password_confirmation' => 'Password123!',
        ])->assertRedirect(route('dashboard.staff.index'));

        $this->assertTrue(User::where('email', 'hari@ramtours.com.np')->first()->canAccessAdmin('reports'));
    }

    public function test_review_submission_awaits_approval(): void
    {
        $this->post(route('testimonials.store'), ['name' => 'Gita', 'rating' => 5, 'content' => 'Wonderful Pokhara trip, everything was perfect!'])->assertRedirect();
        $this->get('/reviews')->assertDontSee('Wonderful Pokhara trip');
    }

    public function test_language_and_currency_switch(): void
    {
        $this->get(route('locale.switch', 'ne'))->assertRedirect();
        $this->assertSame('ne', session('locale'));

        $this->post(route('currency.switch'), ['currency' => 'USD'])->assertRedirect();
        $this->get('/tours')->assertOk()->assertSee('$ ', false);
    }
}
