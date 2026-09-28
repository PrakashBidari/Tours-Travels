<?php

/**
 * Ram Tours & Travel — site-wide travel configuration.
 *
 * Company contact details here are only defaults: anything an admin saves under
 * Dashboard → Page Settings takes precedence (see App\Support\Site).
 */

$img = fn (string $id, int $w = 1200) => "https://images.unsplash.com/{$id}?auto=format&fit=crop&w={$w}&q=75";

return [

    'company' => [
        'name' => 'Ram Tours & Travel Pvt. Ltd.',
        'short_name' => 'Ram Tours & Travel',
        'tagline' => 'Your Journey, Our Commitment',
        'address' => 'Pabitra Nagar, Gangabu, Kathmandu, Nepal',
        'phone' => '+977 1-5912345',
        'mobile' => '+977 980-1234567',
        'whatsapp' => '9779801234567',
        'email' => 'info@ramtours.com.np',
        'office_hours' => 'Sun – Fri: 9:00 AM – 6:00 PM · Sat: 10:00 AM – 2:00 PM',
        'emergency' => '+977 980-1234567',
        'messenger' => 'https://m.me/ramtours',
        'map_embed' => 'https://www.google.com/maps?q=Gangabu,+Kathmandu,+Nepal&output=embed',
        'registration' => 'Reg. No. 000000/080/081 · PAN 000000000',
        'affiliations' => ['Nepal Tourism Board (NTB)', 'NATA Nepal', 'IATA Accredited Agent', 'TAAN Member'],
        'founded' => 2010,
    ],

    // Default base currency every price in the database is stored in.
    'currency' => 'NPR',

    // Indicative conversion rates from 1 NPR — used by the header currency switcher only;
    // bookings are always charged in NPR.
    'currencies' => [
        'NPR' => ['symbol' => 'Rs.', 'rate' => 1],
        'USD' => ['symbol' => '$', 'rate' => 0.0075],
        'AED' => ['symbol' => 'AED', 'rate' => 0.0275],
        'INR' => ['symbol' => '₹', 'rate' => 0.625],
        'EUR' => ['symbol' => '€', 'rate' => 0.0069],
    ],

    'images' => [
        'hero' => $img('photo-1544735716-392fe2489ffa', 1920),
        'plane' => $img('photo-1436491865332-7a61a109cc05', 900),
        'plane_landing' => $img('photo-1556388158-158ea5ccacbd', 1200),
        'bus' => $img('photo-1544620347-c4fd4a3d5957', 900),
        'car' => $img('photo-1533473359331-0135ef1b58bf', 900),
        'hotel' => $img('photo-1618773928121-c32242e63f39', 900),
        'visa' => $img('photo-1488646953014-85cb44e25828', 900),
        'trekking' => $img('photo-1551632811-561732d1e306', 900),
        'traveler' => $img('photo-1503220317375-aaad61436b1b', 1000),
        'mountains' => $img('photo-1506905925346-21bda4d32df4', 1920),
        'group' => $img('photo-1539635278303-d4002c07eae3', 1000),
        'kathmandu' => $img('photo-1623492701902-47dc207df5dc', 800),
        'boudhanath' => $img('photo-1605640840605-14ac1855827b', 800),
        'swayambhu' => $img('photo-1558799401-1dcba79834c2', 800),
        'pokhara' => $img('photo-1571401835393-8c5f35328320', 800),
        'phewa' => $img('photo-1596895111956-bf1cf0599ce5', 800),
        'chitwan' => $img('photo-1535338454770-8be927b5a00b', 800),
        'elephant' => $img('photo-1535941339077-2dd1c7963098', 800),
        'lumbini' => $img('photo-1526712318848-5f38e2740d44', 800),
        'mustang' => $img('photo-1585409677983-0f6c41ca9c3b', 800),
        'everest' => $img('photo-1533130061792-64b345e4a833', 800),
        'ama_dablam' => $img('photo-1486911278844-a81c5267e227', 800),
        'rara' => $img('photo-1598091383021-15ddea10925d', 800),
        'langtang' => $img('photo-1581791534721-e599df4417f7', 800),
        'sunrise' => $img('photo-1506905925346-21bda4d32df4', 800),
        'viewpoint' => $img('photo-1533587851505-d119e13fa0d7', 800),
        'rafting' => $img('photo-1530866495561-507c9faab2ed', 800),
        'camping' => $img('photo-1559521783-1d1599583485', 800),
        'dubai' => $img('photo-1512453979798-5ea266f8880c', 800),
        'thailand' => $img('photo-1552465011-b4e21bf6e79a', 800),
        'bangkok' => $img('photo-1508009603885-50cf7c579365', 800),
        'bali' => $img('photo-1537996194471-e657df975ab4', 800),
        'maldives' => $img('photo-1514282401047-d79a71a590e8', 800),
        'maldives2' => $img('photo-1590523277543-a94d2e4eb00b', 800),
        'singapore' => $img('photo-1525625293386-3f8f99389edd', 800),
        'malaysia' => $img('photo-1596422846543-75c6fc197f07', 800),
        'vietnam' => $img('photo-1583417319070-4a69db38a482', 800),
        'japan' => $img('photo-1493976040374-85c8e12f0c0e', 800),
        'korea' => $img('photo-1538485399081-7191377e8241', 800),
        'india' => $img('photo-1524492412937-b28074a5d7da', 800),
        'srilanka' => $img('photo-1566296314736-6eaac1ca0cb9', 800),
        'bhutan' => $img('photo-1528181304800-259b08848526', 800),
        'europe' => $img('photo-1569949381669-ecf31ae8e613', 800),
        'europe_lake' => $img('photo-1501785888041-af3ef285b470', 800),
        'australia' => $img('photo-1506973035872-a4ec16b8e8d9', 800),
        'usa' => $img('photo-1485738422979-f5c462d49f74', 800),
        'resort' => $img('photo-1520250497591-112f2f40a3f4', 800),
        'hotel_room' => $img('photo-1631049307264-da0ec9d70304', 800),
        'hotel_pool' => $img('photo-1566073771259-6a8506099945', 800),
        'luxury_car' => $img('photo-1544636331-e26879cd4d9b', 800),
        'beach' => $img('photo-1507525428034-b723cf961d3e', 800),
        'avatar_1' => $img('photo-1494790108377-be9c29b29330', 160),
        'avatar_2' => $img('photo-1507003211169-0a1dd7228f2d', 160),
        'avatar_3' => $img('photo-1438761681033-6461ffad8d80', 160),
        'avatar_4' => $img('photo-1500648767791-00dcc994a43e', 160),
    ],

    'airlines' => [
        'domestic' => ['Buddha Air', 'Yeti Airlines', 'Shree Airlines', 'Nepal Airlines', 'Tara Air', 'Summit Air'],
        'international' => [
            'Nepal Airlines', 'Himalaya Airlines', 'Qatar Airways', 'flydubai', 'Air Arabia', 'Emirates',
            'Thai Airways', 'Singapore Airlines', 'Etihad Airways', 'Turkish Airlines', 'Malaysia Airlines', 'Air India',
        ],
    ],

    'airports' => [
        'KTM' => 'Kathmandu (KTM)', 'PKR' => 'Pokhara (PKR)', 'BHR' => 'Bharatpur (BHR)', 'BWA' => 'Bhairahawa (BWA)',
        'BIR' => 'Biratnagar (BIR)', 'KEP' => 'Nepalgunj (KEP)', 'JKR' => 'Janakpur (JKR)', 'TMI' => 'Tumlingtar (TMI)',
        'LUA' => 'Lukla (LUA)', 'DHI' => 'Dhangadhi (DHI)', 'BDP' => 'Bhadrapur (BDP)', 'SIF' => 'Simara (SIF)',
        'DXB' => 'Dubai (DXB)', 'DOH' => 'Doha (DOH)', 'BKK' => 'Bangkok (BKK)', 'KUL' => 'Kuala Lumpur (KUL)',
        'SIN' => 'Singapore (SIN)', 'DEL' => 'New Delhi (DEL)', 'DPS' => 'Bali (DPS)', 'MLE' => 'Malé (MLE)',
        'ICN' => 'Seoul (ICN)', 'NRT' => 'Tokyo (NRT)', 'IST' => 'Istanbul (IST)', 'LHR' => 'London (LHR)',
        'SYD' => 'Sydney (SYD)', 'JFK' => 'New York (JFK)', 'PBH' => 'Paro (PBH)', 'CMB' => 'Colombo (CMB)',
    ],

    'bus_cities' => [
        'Kathmandu', 'Pokhara', 'Chitwan', 'Butwal', 'Lumbini', 'Dharan', 'Nepalgunj', 'Biratnagar', 'Janakpur', 'Birgunj',
    ],

    'vehicle_categories' => [
        'suv' => 'SUV', 'jeep' => 'Jeep', 'hiace' => 'Hiace / Van', 'scorpio' => 'Scorpio', 'sedan' => 'Sedan',
        'ev' => 'Electric (EV)', 'luxury' => 'Luxury', 'coaster' => 'Coaster / Bus',
    ],

    'tour_categories' => [
        'nepal' => 'Nepal Tours',
        'international' => 'International Tours',
        'trekking' => 'Trekking',
        'adventure' => 'Adventure Activities',
    ],

    'trip_styles' => ['family' => 'Family', 'couple' => 'Couple / Honeymoon', 'adventure' => 'Adventure', 'group' => 'Group', 'pilgrimage' => 'Pilgrimage', 'luxury' => 'Luxury'],

    'seasons' => ['all' => 'All Year', 'spring' => 'Spring (Mar–May)', 'summer' => 'Summer (Jun–Aug)', 'autumn' => 'Autumn (Sep–Nov)', 'winter' => 'Winter (Dec–Feb)'],

    'visa_types' => ['tourist' => 'Tourist Visa', 'visit' => 'Visit Visa', 'student' => 'Student Visa', 'work' => 'Work Visa', 'business' => 'Business Visa', 'transit' => 'Transit Visa'],

    'payment_methods' => [
        'esewa' => ['label' => 'eSewa', 'group' => 'nepal', 'color' => '#60bb46'],
        'khalti' => ['label' => 'Khalti', 'group' => 'nepal', 'color' => '#5c2d91'],
        'fonepay' => ['label' => 'FonePay', 'group' => 'nepal', 'color' => '#d7282f'],
        'connectips' => ['label' => 'ConnectIPS', 'group' => 'nepal', 'color' => '#1a4c9c'],
        'imepay' => ['label' => 'IME Pay', 'group' => 'nepal', 'color' => '#ed1c24'],
        'card' => ['label' => 'Visa / MasterCard', 'group' => 'international', 'color' => '#1a1f71'],
        'paypal' => ['label' => 'PayPal', 'group' => 'international', 'color' => '#003087'],
        'office' => ['label' => 'Pay at Office / Bank Transfer', 'group' => 'offline', 'color' => '#0047ab'],
    ],

    // eSewa ePay v2. The defaults are eSewa's public sandbox credentials; set real
    // merchant values in .env before going live.
    'esewa' => [
        'product_code' => env('ESEWA_PRODUCT_CODE', 'EPAYTEST'),
        'secret_key' => env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'),
        'form_url' => env('ESEWA_FORM_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'),
        'status_url' => env('ESEWA_STATUS_URL', 'https://rc.esewa.com.np/api/epay/transaction/status/'),
    ],

    // Khalti ePayment (KPG-2). Leave the key empty to show Khalti as "coming soon".
    'khalti' => [
        'secret_key' => env('KHALTI_SECRET_KEY'),
        'base_url' => env('KHALTI_BASE_URL', 'https://dev.khalti.com/api/v2/'),
    ],

    'service_types' => [
        'tour' => 'Tour Package',
        'flight' => 'Flight Ticket',
        'bus' => 'Bus Ticket',
        'car' => 'Car Rental',
        'hotel' => 'Hotel',
        'visa' => 'Visa Service',
    ],

    /*
     * Staff roles and the admin modules each can open. Super admins can open everything,
     * including partner (vendor), revenue, user and website settings screens.
     */
    'staff_roles' => [
        'manager' => ['label' => 'Manager', 'modules' => ['bookings', 'catalog', 'inquiries', 'customers', 'marketing', 'content', 'reports']],
        'ticketing' => ['label' => 'Ticketing Staff', 'modules' => ['bookings', 'inquiries', 'customers']],
        'consultant' => ['label' => 'Travel Consultant', 'modules' => ['bookings', 'inquiries', 'customers', 'catalog']],
        'editor' => ['label' => 'Content Editor', 'modules' => ['content', 'marketing', 'catalog']],
        'finance' => ['label' => 'Finance', 'modules' => ['bookings', 'customers', 'reports']],
    ],

    'booking_statuses' => ['pending', 'confirmed', 'processing', 'ticketed', 'completed', 'cancelled', 'refunded'],
    'payment_statuses' => ['unpaid', 'pending', 'paid', 'failed', 'refunded'],

    'inquiry_types' => [
        'quote' => 'Quote Request',
        'flight' => 'Flight Booking Request',
        'pnr' => 'PNR Inquiry',
        'fare' => 'Fare Inquiry',
        'reissue' => 'Ticket Reissue',
        'refund' => 'Refund Request',
        'visa' => 'Visa Inquiry',
        'bus' => 'Bus Inquiry',
        'general' => 'General Inquiry',
    ],
];
