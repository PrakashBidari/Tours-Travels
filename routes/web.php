<?php

use App\Http\Controllers\Account\BookingDetailsController;
use App\Http\Controllers\Account\CartController;
use App\Http\Controllers\Auth\PendingRegistrationController;
use App\Http\Controllers\EditorImageController;
use App\Http\Controllers\Account\DashboardController as AccountDashboardController;
use App\Http\Controllers\Account\RentalRequestController;
use App\Http\Controllers\Account\TravelBookingController;
use App\Http\Controllers\Account\TripController;
use App\Http\Controllers\Account\WishlistController;
use App\Http\Controllers\Backend\BookingController as BackendBookingController;
use App\Http\Controllers\Backend\DashboardController as BackendDashboardController;
use App\Http\Controllers\Backend\DestinationController as BackendDestinationController;
use App\Http\Controllers\Backend\ContactMessageController;
use App\Http\Controllers\Backend\HeaderSettingController;
use App\Http\Controllers\Backend\HomeOrderController;
use App\Http\Controllers\Backend\PackageController as BackendPackageController;
use App\Http\Controllers\Backend\PageSettingController;
use App\Http\Controllers\Backend\RentalPartnerController;
use App\Http\Controllers\Backend\RevenueController;
use App\Http\Controllers\Backend\UserController as BackendUserController;
use App\Http\Controllers\Backend\VendorController;
use App\Http\Controllers\Backend\BlogPostController;
use App\Http\Controllers\Backend\BusRouteController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\GalleryItemController;
use App\Http\Controllers\Backend\InquiryController as BackendInquiryController;
use App\Http\Controllers\Backend\OfferController;
use App\Http\Controllers\Backend\ServiceBookingController;
use App\Http\Controllers\Backend\StaffController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\TourPackageController;
use App\Http\Controllers\Backend\VehicleController as BackendVehicleController;
use App\Http\Controllers\Backend\VisaServiceController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\BookingController as FrontendBookingController;
use App\Http\Controllers\Frontend\BusController;
use App\Http\Controllers\Frontend\CarController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\ContentController;
use App\Http\Controllers\Frontend\FlightController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\TourController;
use App\Http\Controllers\Frontend\VisaController;
use App\Http\Controllers\Frontend\DestinationController as FrontendDestinationController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PropertyController;
use App\Http\Controllers\Frontend\SearchController;
use App\Http\Controllers\Frontend\VehicleRentalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RentalPartner\DashboardController as RentalPartnerDashboardController;
use App\Http\Controllers\RentalPartner\RequestController as RentalPartnerRequestController;
use App\Http\Controllers\Vendor\BookingController as VendorBookingController;
use App\Http\Controllers\Vendor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Vendor\DestinationController as VendorDestinationController;
use App\Http\Controllers\Vendor\PackageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Jetstream\Jetstream;

Route::get('/', HomeController::class)->name('home');
Route::get('/destinations', [FrontendDestinationController::class, 'index'])->name('destinations.index');
Route::get('/search', SearchController::class)->name('search');
Route::get('/properties/{property:slug}', [PropertyController::class, 'show'])->name('property.show');
Route::get('/about', AboutController::class)->name('about');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
// ---- Ram Tours services -------------------------------------------------------
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/compare', [TourController::class, 'compare'])->name('tours.compare');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');
Route::get('/tours/{tour}/itinerary.pdf', [TourController::class, 'itinerary'])->name('tours.itinerary');
Route::post('/tours/{tour}/book', [TourController::class, 'book'])->middleware('throttle:10,1')->name('tours.book');

Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');
Route::post('/flights/book', [FlightController::class, 'book'])->middleware('throttle:10,1')->name('flights.book');
Route::post('/flights/inquiry', [FlightController::class, 'inquiry'])->middleware('throttle:10,1')->name('flights.inquiry');

Route::get('/bus-tickets', [BusController::class, 'index'])->name('bus.index');
Route::get('/bus-tickets/search', [BusController::class, 'search'])->name('bus.search');
Route::get('/bus-tickets/{busRoute}/seats', [BusController::class, 'seats'])->name('bus.seats');
Route::post('/bus-tickets/{busRoute}/book', [BusController::class, 'book'])->middleware('throttle:10,1')->name('bus.book');

Route::get('/car-rental', [CarController::class, 'index'])->name('cars.index');
Route::get('/car-rental/{vehicle}', [CarController::class, 'show'])->name('cars.show');
Route::post('/car-rental/{vehicle}/book', [CarController::class, 'book'])->middleware('throttle:10,1')->name('cars.book');

Route::redirect('/hotels', '/search')->name('hotels.index');

Route::get('/visa', [VisaController::class, 'index'])->name('visa.index');
Route::get('/visa/{visa}', [VisaController::class, 'show'])->name('visa.show');
Route::post('/visa/{visa}/apply', [VisaController::class, 'apply'])->middleware('throttle:10,1')->name('visa.apply');

// Booking confirmation, invoice & payment are signed links (guests can book without an account).
Route::get('/track-booking', [FrontendBookingController::class, 'trackForm'])->name('booking.track');
Route::post('/track-booking', [FrontendBookingController::class, 'track'])->middleware('throttle:10,1')->name('booking.track.lookup');
Route::middleware('signed')->group(function () {
    Route::get('/booking/{booking}', [FrontendBookingController::class, 'show'])->name('booking.show');
    Route::get('/booking/{booking}/invoice', [FrontendBookingController::class, 'invoice'])->name('booking.invoice');
    Route::get('/booking/{booking}/pay', [PaymentController::class, 'show'])->name('booking.pay');
    Route::post('/booking/{booking}/pay', [PaymentController::class, 'start'])->name('booking.pay.start');
});
Route::get('/payment/esewa/success', [PaymentController::class, 'esewaSuccess'])->name('payment.esewa.success');
Route::get('/payment/esewa/failure', [PaymentController::class, 'esewaFailure'])->name('payment.esewa.failure');
Route::get('/payment/khalti/callback', [PaymentController::class, 'khaltiCallback'])->name('payment.khalti.callback');

Route::get('/offers', [ContentController::class, 'offers'])->name('offers.index');
Route::post('/coupons/check', [ContentController::class, 'checkCoupon'])->middleware('throttle:20,1')->name('coupons.check');
Route::get('/blog', [ContentController::class, 'blog'])->name('blog.index');
Route::get('/blog/{post}', [ContentController::class, 'post'])->name('blog.show');
Route::get('/gallery', [ContentController::class, 'gallery'])->name('gallery.index');
Route::get('/reviews', [ContentController::class, 'testimonials'])->name('testimonials.index');
Route::post('/reviews', [ContentController::class, 'storeTestimonial'])->middleware('throttle:5,1')->name('testimonials.store');
Route::get('/faq', [ContentController::class, 'faq'])->name('faq');
Route::get('/refund-policy', [ContentController::class, 'refundPolicy'])->name('pages.refund');
Route::get('/cookie-policy', [ContentController::class, 'cookiePolicy'])->name('pages.cookies');
Route::post('/newsletter', [ContentController::class, 'subscribe'])->middleware('throttle:5,1')->name('newsletter.subscribe');
Route::post('/inquiry', [ContentController::class, 'inquiry'])->middleware('throttle:10,1')->name('inquiry.store');
Route::get('/locale/{locale}', [ContentController::class, 'locale'])->name('locale.switch');
Route::post('/currency', [ContentController::class, 'currency'])->name('currency.switch');
Route::get('/sitemap.xml', [ContentController::class, 'sitemap'])->name('sitemap');

Route::get('/rent-vehicle', [VehicleRentalController::class, 'create'])->name('rent-vehicle');
Route::post('/rent-vehicle', [VehicleRentalController::class, 'store'])->middleware(['auth', 'role:user'])->name('rent-vehicle.store');

// Registration is deliberately kept out of Fortify (see config/fortify.php)
// so a User row is only created once the emailed confirmation link is clicked.
Route::middleware('guest:'.config('fortify.guard'))->group(function () {
    Route::get('/register', [PendingRegistrationController::class, 'create'])->name('register');
    Route::post('/register', [PendingRegistrationController::class, 'store']);
});

Route::get('/register/confirm/{token}', [PendingRegistrationController::class, 'confirm'])
    ->middleware('signed')
    ->name('register.confirm');

// Jetstream normally registers these routes itself (see vendor/laravel/jetstream/routes/livewire.php)
// with controllers that hardcode their view names. We call Jetstream::ignoreRoutes() in
// JetstreamServiceProvider and re-declare the same routes here so they render the relocated
// views under resources/views/backend and resources/views/frontend instead.
Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {
    if (Jetstream::hasTermsAndPrivacyPolicyFeature()) {
        Route::get('/terms-of-service', function () {
            return view('frontend.terms', [
                'terms' => Str::markdown(file_get_contents(Jetstream::localizedMarkdownPath('terms.md'))),
            ]);
        })->name('terms.show');

        Route::get('/privacy-policy', function () {
            return view('frontend.policy', [
                'policy' => Str::markdown(file_get_contents(Jetstream::localizedMarkdownPath('policy.md'))),
            ]);
        })->name('policy.show');
    }

    $authMiddleware = config('jetstream.guard')
        ? 'auth:'.config('jetstream.guard')
        : 'auth';

    $authSessionMiddleware = config('jetstream.auth_session', false)
        ? config('jetstream.auth_session')
        : null;

    Route::group(['middleware' => array_values(array_filter([$authMiddleware, $authSessionMiddleware]))], function () {
        Route::get('/user/profile', function (Request $request) {
            return view('backend.profile.show', [
                'request' => $request,
                'user' => $request->user(),
            ]);
        })->name('profile.show');

        Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

        Route::post('/editor-images', [EditorImageController::class, 'store'])->name('editor-images.store');

        Route::group(['middleware' => 'verified'], function () {
            if (Jetstream::hasApiFeatures()) {
                Route::get('/user/api-tokens', function (Request $request) {
                    return view('backend.api.index', [
                        'request' => $request,
                        'user' => $request->user(),
                    ]);
                })->name('api-tokens.index');
            }

            // Ram Tours admin — super admins and staff; each module is gated by staff role.
            Route::group(['middleware' => 'role:super_admin,staff'], function () {
                Route::get('/dashboard', BackendDashboardController::class)->name('dashboard');

                Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
                    Route::middleware('admin.can:bookings')->group(function () {
                        Route::get('/travel-bookings', [ServiceBookingController::class, 'index'])->name('service-bookings.index');
                        Route::get('/travel-bookings/export', [ServiceBookingController::class, 'export'])->name('service-bookings.export');
                        Route::get('/travel-bookings/{serviceBooking}', [ServiceBookingController::class, 'show'])->name('service-bookings.show');
                        Route::put('/travel-bookings/{serviceBooking}', [ServiceBookingController::class, 'update'])->name('service-bookings.update');
                        Route::delete('/travel-bookings/{serviceBooking}', [ServiceBookingController::class, 'destroy'])->name('service-bookings.destroy');
                        Route::get('/travel-bookings/{serviceBooking}/documents/{index}', [ServiceBookingController::class, 'document'])->whereNumber('index')->name('service-bookings.document');
                    });

                    Route::middleware('admin.can:catalog')->group(function () {
                        Route::resource('tour-packages', TourPackageController::class)->except('show');
                        Route::resource('vehicles', BackendVehicleController::class)->except('show');
                        Route::resource('bus-routes', BusRouteController::class)->except('show');
                        Route::get('/bus-routes/{busRoute}/seats', [BusRouteController::class, 'seats'])->name('bus-routes.seats');
                        Route::resource('visa-services', VisaServiceController::class)->except('show');
                    });

                    Route::middleware('admin.can:inquiries')->group(function () {
                        Route::resource('inquiries', BackendInquiryController::class)->only(['index', 'show', 'update', 'destroy']);
                    });

                    Route::middleware('admin.can:customers')->group(function () {
                        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
                        Route::get('/customers/{email}', [CustomerController::class, 'show'])->where('email', '[^/]+')->name('customers.show');
                    });

                    Route::middleware('admin.can:marketing')->group(function () {
                        Route::resource('offers', OfferController::class)->except('show');
                        Route::resource('testimonials', TestimonialController::class)->except('show');
                        Route::post('/testimonials/{testimonial}/toggle', [TestimonialController::class, 'toggle'])->name('testimonials.toggle');
                        Route::get('/subscribers', [CustomerController::class, 'subscribers'])->name('subscribers.index');
                        Route::get('/subscribers/export', [CustomerController::class, 'exportSubscribers'])->name('subscribers.export');
                        Route::delete('/subscribers/{subscriber}', [CustomerController::class, 'destroySubscriber'])->name('subscribers.destroy');
                    });

                    Route::middleware('admin.can:content')->group(function () {
                        Route::resource('blog-posts', BlogPostController::class)->except('show');
                        Route::resource('faqs', FaqController::class)->except('show');
                        Route::get('/gallery', [GalleryItemController::class, 'index'])->name('gallery.index');
                        Route::post('/gallery', [GalleryItemController::class, 'store'])->name('gallery.store');
                        Route::put('/gallery/{galleryItem}', [GalleryItemController::class, 'update'])->name('gallery.update');
                        Route::delete('/gallery/{galleryItem}', [GalleryItemController::class, 'destroy'])->name('gallery.destroy');
                    });
                });
            });

            // Super Admin
            Route::group(['middleware' => 'role:super_admin'], function () {
                Route::resource('dashboard/staff', StaffController::class)->except('show')->parameters(['staff' => 'staff'])->names('dashboard.staff');

                Route::group(['as' => 'dashboard.'], function () {
                    Route::get('/dashboard/vendors', [VendorController::class, 'index'])->name('vendors.index');
                    Route::get('/dashboard/vendors/create', [VendorController::class, 'create'])->name('vendors.create');
                    Route::post('/dashboard/vendors', [VendorController::class, 'store'])->name('vendors.store');
                    Route::post('/dashboard/vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
                    Route::post('/dashboard/vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('vendors.reject');

                    Route::get('/dashboard/rental-partners', [RentalPartnerController::class, 'index'])->name('rental-partners.index');
                    Route::get('/dashboard/rental-partners/create', [RentalPartnerController::class, 'create'])->name('rental-partners.create');
                    Route::post('/dashboard/rental-partners', [RentalPartnerController::class, 'store'])->name('rental-partners.store');
                    Route::post('/dashboard/rental-partners/{rentalPartner}/approve', [RentalPartnerController::class, 'approve'])->name('rental-partners.approve');
                    Route::post('/dashboard/rental-partners/{rentalPartner}/reject', [RentalPartnerController::class, 'reject'])->name('rental-partners.reject');

                    Route::get('/dashboard/packages', [BackendPackageController::class, 'index'])->name('packages.index');
                    Route::get('/dashboard/packages/create', [BackendPackageController::class, 'create'])->name('packages.create');
                    Route::post('/dashboard/packages', [BackendPackageController::class, 'store'])->name('packages.store');
                    Route::get('/dashboard/packages/{package}/edit', [BackendPackageController::class, 'edit'])->name('packages.edit');
                    Route::put('/dashboard/packages/{package}', [BackendPackageController::class, 'update'])->name('packages.update');
                    Route::delete('/dashboard/packages/{package}', [BackendPackageController::class, 'destroy'])->name('packages.destroy');
                    Route::post('/dashboard/packages/{package}/approve', [BackendPackageController::class, 'approve'])->name('packages.approve');
                    Route::post('/dashboard/packages/{package}/reject', [BackendPackageController::class, 'reject'])->name('packages.reject');
                    Route::patch('/dashboard/packages/{package}/category', [BackendPackageController::class, 'updateCategory'])->name('packages.update-category');

                    Route::get('/dashboard/destinations', [BackendDestinationController::class, 'index'])->name('destinations.index');
                    Route::get('/dashboard/destinations/create', [BackendDestinationController::class, 'create'])->name('destinations.create');
                    Route::post('/dashboard/destinations', [BackendDestinationController::class, 'store'])->name('destinations.store');
                    Route::get('/dashboard/destinations/{destination}/edit', [BackendDestinationController::class, 'edit'])->name('destinations.edit');
                    Route::put('/dashboard/destinations/{destination}', [BackendDestinationController::class, 'update'])->name('destinations.update');
                    Route::delete('/dashboard/destinations/{destination}', [BackendDestinationController::class, 'destroy'])->name('destinations.destroy');
                    Route::post('/dashboard/destinations/{destination}/approve', [BackendDestinationController::class, 'approve'])->name('destinations.approve');
                    Route::post('/dashboard/destinations/{destination}/reject', [BackendDestinationController::class, 'reject'])->name('destinations.reject');

                    Route::get('/dashboard/home-order', [HomeOrderController::class, 'index'])->name('home-order.index');
                    Route::post('/dashboard/home-order/destinations', [HomeOrderController::class, 'updateDestinations'])->name('home-order.destinations');
                    Route::post('/dashboard/home-order/packages', [HomeOrderController::class, 'updatePackages'])->name('home-order.packages');

                    Route::get('/dashboard/bookings', [BackendBookingController::class, 'index'])->name('bookings.index');

                    Route::get('/dashboard/revenue', [RevenueController::class, 'index'])->name('revenue.index');
                    Route::get('/dashboard/revenue/pdf', [RevenueController::class, 'pdf'])->name('revenue.pdf');
                    Route::get('/dashboard/revenue/doc', [RevenueController::class, 'doc'])->name('revenue.doc');

                    Route::get('/dashboard/users', [BackendUserController::class, 'index'])->name('users.index');
                    Route::get('/dashboard/users/create', [BackendUserController::class, 'create'])->name('users.create');
                    Route::post('/dashboard/users', [BackendUserController::class, 'store'])->name('users.store');

                    Route::get('/dashboard/page-settings', [PageSettingController::class, 'index'])->name('page-settings.index');
                    Route::put('/dashboard/page-settings', [PageSettingController::class, 'update'])->name('page-settings.update');
                    Route::put('/dashboard/page-settings/social-links', [PageSettingController::class, 'updateSocialLinks'])->name('page-settings.social-links.update');
                    Route::post('/dashboard/page-settings/footer-columns', [PageSettingController::class, 'storeFooterColumn'])->name('page-settings.footer-columns.store');
                    Route::put('/dashboard/page-settings/footer-columns/{footerColumn}', [PageSettingController::class, 'updateFooterColumn'])->name('page-settings.footer-columns.update');
                    Route::delete('/dashboard/page-settings/footer-columns/{footerColumn}', [PageSettingController::class, 'destroyFooterColumn'])->name('page-settings.footer-columns.destroy');
                    Route::post('/dashboard/page-settings/footer-links', [PageSettingController::class, 'storeFooterLink'])->name('page-settings.footer-links.store');
                    Route::put('/dashboard/page-settings/footer-links/{footerLink}', [PageSettingController::class, 'updateFooterLink'])->name('page-settings.footer-links.update');
                    Route::delete('/dashboard/page-settings/footer-links/{footerLink}', [PageSettingController::class, 'destroyFooterLink'])->name('page-settings.footer-links.destroy');

                    Route::get('/dashboard/header-settings', [HeaderSettingController::class, 'index'])->name('header-settings.index');
                    Route::put('/dashboard/header-settings/general', [HeaderSettingController::class, 'updateGeneral'])->name('header-settings.general.update');
                    Route::post('/dashboard/header-settings/menu-items', [HeaderSettingController::class, 'storeMenuItem'])->name('header-settings.menu-items.store');
                    Route::put('/dashboard/header-settings/menu-items/{navMenuItem}', [HeaderSettingController::class, 'updateMenuItem'])->name('header-settings.menu-items.update');
                    Route::delete('/dashboard/header-settings/menu-items/{navMenuItem}', [HeaderSettingController::class, 'destroyMenuItem'])->name('header-settings.menu-items.destroy');
                    Route::post('/dashboard/header-settings/menu-items/reorder', [HeaderSettingController::class, 'reorderMenuItems'])->name('header-settings.menu-items.reorder');
                    Route::post('/dashboard/header-settings/buttons', [HeaderSettingController::class, 'storeButton'])->name('header-settings.buttons.store');
                    Route::put('/dashboard/header-settings/buttons/{headerButton}', [HeaderSettingController::class, 'updateButton'])->name('header-settings.buttons.update');
                    Route::delete('/dashboard/header-settings/buttons/{headerButton}', [HeaderSettingController::class, 'destroyButton'])->name('header-settings.buttons.destroy');
                    Route::post('/dashboard/header-settings/buttons/reorder', [HeaderSettingController::class, 'reorderButtons'])->name('header-settings.buttons.reorder');

                    Route::get('/dashboard/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
                    Route::delete('/dashboard/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
                });
            });

            // Vendor
            Route::group(['prefix' => 'vendor', 'middleware' => 'role:vendor', 'as' => 'vendor.'], function () {
                Route::get('/dashboard', VendorDashboardController::class)->name('dashboard');

                Route::group(['middleware' => 'vendor.approved'], function () {
                    Route::get('/destinations', [VendorDestinationController::class, 'index'])->name('destinations.index');
                    Route::get('/destinations/create', [VendorDestinationController::class, 'create'])->name('destinations.create');
                    Route::post('/destinations', [VendorDestinationController::class, 'store'])->name('destinations.store');
                    Route::get('/destinations/{destination}/edit', [VendorDestinationController::class, 'edit'])->name('destinations.edit');
                    Route::put('/destinations/{destination}', [VendorDestinationController::class, 'update'])->name('destinations.update');
                    Route::delete('/destinations/{destination}', [VendorDestinationController::class, 'destroy'])->name('destinations.destroy');

                    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
                    Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
                    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
                    Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
                    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
                    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

                    Route::get('/bookings', [VendorBookingController::class, 'index'])->name('bookings.index');
                    Route::patch('/bookings/{booking}/status', [VendorBookingController::class, 'updateStatus'])->name('bookings.status');
                });
            });

            // Rental Partner
            Route::group(['prefix' => 'rental-partner', 'middleware' => 'role:rental_partner', 'as' => 'rental-partner.'], function () {
                Route::get('/dashboard', RentalPartnerDashboardController::class)->name('dashboard');

                Route::group(['middleware' => 'rental-partner.approved'], function () {
                    Route::get('/requests', [RentalPartnerRequestController::class, 'index'])->name('requests.index');
                    Route::get('/requests/applied', [RentalPartnerRequestController::class, 'applied'])->name('requests.applied');
                    Route::patch('/requests/{vehicleRentalRequest}/claim', [RentalPartnerRequestController::class, 'claim'])->name('requests.claim');
                    Route::delete('/requests/{vehicleRentalRequest}/unclaim', [RentalPartnerRequestController::class, 'unclaim'])->name('requests.unclaim');
                    Route::patch('/requests/{vehicleRentalRequest}/complete', [RentalPartnerRequestController::class, 'complete'])->name('requests.complete');
                });
            });

            // User account
            Route::group(['prefix' => 'account', 'middleware' => 'role:user', 'as' => 'account.'], function () {
                Route::get('/dashboard', AccountDashboardController::class)->name('dashboard');

                Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
                Route::get('/travel-bookings', [TravelBookingController::class, 'index'])->name('travel-bookings.index');
                Route::delete('/trips/{booking}', [TripController::class, 'destroy'])->name('trips.destroy');

                Route::get('/rental-requests', [RentalRequestController::class, 'index'])->name('rental-requests.index');
                Route::patch('/rental-requests/{vehicleRentalRequest}/accept', [RentalRequestController::class, 'accept'])->name('rental-requests.accept');
                Route::patch('/rental-requests/{vehicleRentalRequest}/reject', [RentalRequestController::class, 'reject'])->name('rental-requests.reject');
                Route::delete('/rental-requests/{vehicleRentalRequest}', [RentalRequestController::class, 'destroy'])->name('rental-requests.destroy');

                Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
                Route::post('/wishlist/{property}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

                Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
                Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
                Route::post('/cart/book-now', [BookingDetailsController::class, 'start'])->name('cart.book-now');
                Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
                Route::post('/cart/checkout', [BookingDetailsController::class, 'startCheckout'])->name('cart.checkout');

                Route::group(['prefix' => 'booking', 'as' => 'booking.'], function () {
                    Route::get('/details', [BookingDetailsController::class, 'edit'])->name('details');
                    Route::post('/details', [BookingDetailsController::class, 'update'])->name('details.update');
                    Route::get('/confirm', [BookingDetailsController::class, 'confirm'])->name('confirm');
                    Route::post('/confirm', [BookingDetailsController::class, 'store'])->name('confirm.store');
                });
            });
        });
    });
});
