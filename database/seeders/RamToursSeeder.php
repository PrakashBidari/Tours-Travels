<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\BusRoute;
use App\Models\Faq;
use App\Models\FooterColumn;
use App\Models\FooterLink;
use App\Models\GalleryItem;
use App\Models\NavMenuItem;
use App\Models\Offer;
use App\Models\PageSetting;
use App\Models\SocialLink;
use App\Models\Testimonial;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VisaService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo catalogue for Ram Tours & Travel. Idempotent: rows are matched on slug /
 * natural keys so re-running refreshes content without duplicating it.
 */
class RamToursSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->navigation();
        $this->footer();
        $this->tours();
        $this->vehicles();
        $this->busRoutes();
        $this->visas();
        $this->testimonials();
        $this->blog();
        $this->offers();
        $this->faqs();
        $this->gallery();
    }

    protected function img(string $key): string
    {
        return config('travel.images.'.$key);
    }

    protected function settings(): void
    {
        $company = config('travel.company');

        PageSetting::current()->update([
            'company_name' => $company['name'],
            'contact_email' => $company['email'],
            'contact_phone' => $company['phone'],
            'contact_mobile' => $company['mobile'],
            'whatsapp_number' => $company['whatsapp'],
            'contact_address' => $company['address'],
            'office_hours' => $company['office_hours'],
            'hero_script' => 'Explore the World',
            'hero_title' => 'Discover New Destinations',
            'hero_subtitle' => 'Your trusted travel partner for Nepal and International tours, flights, hotels, buses, cars and more.',
            'why_title' => 'Your Trusted Travel Partner',
            'why_description' => 'At Ram Tours & Travel Pvt. Ltd., we are committed to making your travel dreams come true with reliable service, best prices and personalized support.',
            'stat_years' => now()->year - $company['founded'],
            'stat_travelers' => 25000,
            'stat_destinations' => 120,
            'footer_tagline' => 'Ram Tours & Travel Pvt. Ltd. is a government-registered travel agency in Kathmandu offering Nepal & international tours, air tickets, bus tickets, car rental, hotels and visa services.',
            'copyright_text' => '&copy; {year} Ram Tours & Travel Pvt. Ltd. All rights reserved.',
            'about_title' => 'About Ram Tours & Travel',
            'about_description' => '<p>Founded in '.$company['founded'].' in Pabitra Nagar, Gangabu, Kathmandu, Ram Tours & Travel Pvt. Ltd. has grown into one of Nepal\'s most trusted full-service travel agencies. From Himalayan treks and cultural tours to international holidays, airline tickets, tourist buses, vehicle rental and visa processing — we handle every part of your journey under one roof.</p><p>Our team of experienced travel consultants, ticketing specialists and local guides is dedicated to honest pricing, safe travel and memorable experiences.</p>',
            'mission_title' => 'Our Mission',
            'mission_description' => '<p>To make travel simple, safe and affordable for every Nepali and every visitor to Nepal by delivering transparent pricing, reliable bookings and personal support at every step.</p>',
            'vision_title' => 'Our Vision',
            'vision_description' => '<p>To be Nepal\'s most loved travel brand — connecting the Himalayas to the world and the world to the Himalayas.</p>',
            'meta_title' => 'Ram Tours & Travel | Nepal Tours, Flights, Bus Tickets, Car Rental & Visa',
            'meta_description' => 'Book Nepal & international tour packages, domestic and international flights, tourist bus tickets, car rental, hotels and visa services with Ram Tours & Travel, Gangabu, Kathmandu.',
        ]);
    }

    /** Main menu matching the Ram Tours design (the header also renders a built-in fallback). */
    protected function navigation(): void
    {
        NavMenuItem::query()->delete();

        $menu = [
            ['Home', '/'],
            ['About Us', '/about'],
            ['Tour Packages', '/tours', [
                ['Nepal Tour Packages', '/tours?category=nepal'],
                ['International Packages', '/tours?category=international'],
                ['Trekking in Nepal', '/tours?category=trekking'],
                ['Adventure Activities', '/tours?category=adventure'],
                ['Offers & Deals', '/offers'],
            ]],
            ['Flight Booking', '/flights', [
                ['Domestic Flights', '/flights?scope=domestic'],
                ['International Flights', '/flights?scope=international'],
                ['PNR / Fare Inquiry', '/flights#services'],
            ]],
            ['Bus Ticket', '/bus-tickets', [
                ['Search Bus Tickets', '/bus-tickets'],
                ['Kathmandu – Pokhara', '/bus-tickets/search?from=Kathmandu&to=Pokhara'],
                ['Kathmandu – Chitwan', '/bus-tickets/search?from=Kathmandu&to=Chitwan'],
            ]],
            ['Car Rental', '/car-rental', [
                ['All Vehicles', '/car-rental'],
                ['SUV & Jeep', '/car-rental?category=suv'],
                ['Hiace / Van', '/car-rental?category=hiace'],
                ['Luxury Cars', '/car-rental?category=luxury'],
            ]],
            ['Hotel', '/hotels', [
                ['Nepal Hotels', '/search?destination=Nepal'],
                ['International Hotels', '/search'],
            ]],
            ['Visa', '/visa', [
                ['Tourist / Visit Visa', '/visa?type=tourist'],
                ['Student Visa', '/visa?type=student'],
                ['Work Visa', '/visa?type=work'],
            ]],
            ['Blog', '/blog'],
            ['Contact', '/contact'],
        ];

        foreach ($menu as $i => $item) {
            $parent = NavMenuItem::create([
                'label' => $item[0], 'url' => $item[1], 'target' => '_self', 'sort_order' => $i, 'is_active' => true,
            ]);

            foreach ($item[2] ?? [] as $j => $child) {
                NavMenuItem::create([
                    'parent_id' => $parent->id, 'label' => $child[0], 'url' => $child[1],
                    'target' => '_self', 'sort_order' => $j, 'depth' => 1, 'is_active' => true,
                ]);
            }
        }
    }

    protected function footer(): void
    {
        FooterLink::query()->delete();
        FooterColumn::query()->delete();

        $columns = [
            'Nepal Packages' => [
                ['Kathmandu Tours', '/tours?category=nepal&destination=Kathmandu'],
                ['Pokhara Tours', '/tours?category=nepal&destination=Pokhara'],
                ['Chitwan Safari', '/tours?category=nepal&destination=Chitwan'],
                ['Lumbini Tours', '/tours?category=nepal&destination=Lumbini'],
                ['Trekking in Nepal', '/tours?category=trekking'],
                ['Adventure Activities', '/tours?category=adventure'],
            ],
            'Our Services' => [
                ['International Packages', '/tours?category=international'],
                ['Flight Booking', '/flights'],
                ['Bus Ticket Booking', '/bus-tickets'],
                ['Car Rental', '/car-rental'],
                ['Hotel Booking', '/hotels'],
                ['Visa Services', '/visa'],
            ],
            'Quick Links' => [
                ['About Us', '/about'],
                ['Offers & Deals', '/offers'],
                ['Travel Blog', '/blog'],
                ['Gallery', '/gallery'],
                ['FAQ', '/faq'],
                ['Track Booking', '/track-booking'],
            ],
        ];

        $position = 0;
        foreach ($columns as $heading => $links) {
            $column = FooterColumn::create(['heading' => $heading, 'position' => $position++]);

            foreach ($links as $i => [$label, $url]) {
                FooterLink::create(['footer_column_id' => $column->id, 'label' => $label, 'url' => $url, 'position' => $i]);
            }
        }

        SocialLink::query()->delete();

        foreach (['facebook' => 'https://www.facebook.com/ramtours', 'instagram' => 'https://www.instagram.com/ramtours', 'youtube' => 'https://www.youtube.com/@ramtours', 'tiktok' => 'https://www.tiktok.com/@ramtours'] as $platform => $url) {
            SocialLink::create(['platform' => $platform, 'url' => $url, 'position' => $position++]);
        }
    }

    protected function tours(): void
    {
        $standardIncludes = ['Airport / bus-park pickup & drop', 'Accommodation on twin sharing', 'Daily breakfast', 'Private tourist vehicle', 'English-speaking guide', 'All government taxes'];
        $standardExcludes = ['Lunch & dinner unless stated', 'Personal expenses & tips', 'Travel insurance', 'Entry fees not mentioned'];

        $tours = [
            // Nepal
            ['Kathmandu Valley Heritage Tour', 'nepal', 'Kathmandu', 3, 2, 14500, 12900, 'family', 'all', 'kathmandu', ['boudhanath', 'swayambhu'], 'Explore the seven UNESCO World Heritage Sites of the Kathmandu Valley — Durbar Squares, Boudhanath, Swayambhunath and Pashupatinath.', '3-star boutique hotel in Thamel', 'Breakfast', 'Private car / van', true],
            ['Pokhara Lakes & Mountains Escape', 'nepal', 'Pokhara', 4, 3, 22500, 19900, 'couple', 'all', 'pokhara', ['phewa', 'sunrise'], 'Sunrise at Sarangkot, boating on Phewa Lake, World Peace Pagoda, Davis Falls and the Annapurna panorama.', 'Lakeside 4-star resort', 'Breakfast & 1 dinner', 'Tourist bus + private car', true],
            ['Chitwan Jungle Safari', 'nepal', 'Chitwan', 3, 2, 17500, null, 'family', 'winter', 'chitwan', ['elephant', 'rafting'], 'Jeep safari to spot the one-horned rhino, canoe ride, Tharu cultural show and bird watching in Chitwan National Park.', 'Jungle resort in Sauraha', 'All meals', 'Tourist bus', true],
            ['Lumbini Pilgrimage Tour', 'nepal', 'Lumbini', 3, 2, 16000, 14500, 'pilgrimage', 'all', 'lumbini', ['swayambhu'], 'Visit the birthplace of Lord Buddha — Maya Devi Temple, Ashoka Pillar and the international monastic zone.', '3-star hotel near Sacred Garden', 'Breakfast & dinner', 'Private car', true],
            ['Upper Mustang Jeep Tour', 'nepal', 'Mustang', 7, 6, 65000, 59000, 'adventure', 'spring', 'mustang', ['viewpoint'], 'Drive into the forbidden kingdom — Jomsom, Kagbeni, Muktinath and the walled city of Lo Manthang.', 'Best available teahouses & lodges', 'All meals', '4WD jeep', true],
            ['Everest Mountain Flight & Nagarkot', 'nepal', 'Everest Region', 2, 1, 32000, null, 'luxury', 'autumn', 'everest', ['ama_dablam'], 'An unforgettable one-hour mountain flight past Mt. Everest followed by a sunset stay at Nagarkot.', 'Nagarkot hill resort', 'Breakfast & dinner', 'Flight + private car', true],
            ['Rara Lake Expedition', 'nepal', 'Rara', 6, 5, 58000, null, 'adventure', 'autumn', 'rara', ['langtang'], "Fly to Nepal's largest lake — the pristine blue Rara in Mugu — surrounded by pine forests and snow peaks.", 'Lodge / camping', 'All meals', 'Flights + jeep', false],
            ['Bandipur & Gorkha Culture Trail', 'nepal', 'Bandipur', 3, 2, 13500, null, 'family', 'all', 'kathmandu', ['viewpoint'], 'Newari hill town of Bandipur, Siddha Cave and Gorkha Durbar — birthplace of modern Nepal.', 'Heritage guesthouse', 'Breakfast', 'Private car', false],
            ['Ilam Tea Garden Getaway', 'nepal', 'Ilam', 4, 3, 21000, null, 'couple', 'spring', 'langtang', ['sunrise'], 'Rolling tea gardens, Kanyam, Antu Danda sunrise and Mai Pokhari in eastern Nepal.', 'Tea estate homestay', 'Breakfast & dinner', 'Flight + private car', false],
            ['Janakpur Religious Tour', 'nepal', 'Janakpur', 2, 1, 11000, null, 'pilgrimage', 'all', 'swayambhu', ['kathmandu'], 'Janaki Mandir, Ram Mandir and the sacred ponds of the ancient Mithila capital.', '3-star hotel', 'Breakfast', 'Flight + city tour', false],
            ['Bardiya Wildlife Safari', 'nepal', 'Bardiya', 4, 3, 26000, null, 'adventure', 'winter', 'elephant', ['chitwan'], "Track wild Bengal tigers and rhinos in Nepal's most untouched national park.", 'Eco jungle lodge', 'All meals', 'Flight + jeep', false],
            // International
            ['Dubai Delight Holiday', 'international', 'Dubai', 5, 4, 115000, 99000, 'family', 'winter', 'dubai', ['beach'], 'Burj Khalifa, desert safari with BBQ dinner, Dhow cruise, Dubai Mall and Miracle Garden.', '4-star hotel in Deira', 'Breakfast + BBQ dinner', 'Private transfers', true],
            ['Amazing Thailand – Bangkok & Pattaya', 'international', 'Thailand', 6, 5, 89000, 79000, 'couple', 'all', 'thailand', ['bangkok'], 'Coral Island speedboat, Alcazar show, Safari World and Bangkok city & temple tour.', '3-star hotels', 'Breakfast', 'SIC transfers', true],
            ['Bali Honeymoon Special', 'international', 'Bali', 6, 5, 135000, 125000, 'couple', 'all', 'bali', ['beach'], 'Ubud rice terraces, Kintamani volcano, Tanah Lot sunset and a candle-light dinner.', 'Private pool villa', 'Breakfast + 1 dinner', 'Private car', true],
            ['Singapore & Malaysia Combo', 'international', 'Singapore', 7, 6, 155000, null, 'family', 'all', 'singapore', ['malaysia'], 'Sentosa, Universal Studios, Gardens by the Bay, Genting Highlands and KL city tour.', '3-star hotels', 'Breakfast', 'Coach + transfers', true],
            ['Maldives Water Villa Retreat', 'international', 'Maldives', 4, 3, 185000, 169000, 'luxury', 'winter', 'maldives', ['maldives2'], 'Overwater villa, speedboat transfers, snorkelling and sunset cruise in the Indian Ocean.', '5-star water villa', 'Half board', 'Speedboat', true],
            ['Bhutan Happiness Tour', 'international', 'Bhutan', 5, 4, 98000, null, 'family', 'spring', 'bhutan', ['mountains'], "Thimphu, Punakha Dzong and the hike to the iconic Tiger's Nest monastery.", '3-star hotels', 'All meals', 'Private car', false],
            ['Vietnam Discovery', 'international', 'Vietnam', 6, 5, 112000, null, 'group', 'all', 'vietnam', ['thailand'], 'Hanoi old quarter, Ha Long Bay cruise and Ho Chi Minh City highlights.', '3-star hotels', 'Breakfast', 'Domestic flight + transfers', false],
            ['Japan Cherry Blossom Tour', 'international', 'Japan', 7, 6, 320000, null, 'luxury', 'spring', 'japan', ['korea'], 'Tokyo, Mt. Fuji, Kyoto temples and Osaka during sakura season.', '4-star hotels', 'Breakfast', 'Bullet train', false],
            ['Europe Highlights – Paris & Swiss Alps', 'international', 'Europe', 10, 9, 450000, null, 'luxury', 'summer', 'europe', ['europe_lake'], 'Paris, Eiffel Tower, Lucerne, Mt. Titlis and Interlaken.', '4-star hotels', 'Breakfast', 'Coach + train', false],
            ['India Golden Triangle', 'international', 'India', 5, 4, 55000, null, 'family', 'winter', 'india', ['india'], 'Delhi, Agra (Taj Mahal) and Jaipur — the classic Golden Triangle.', '3-star hotels', 'Breakfast', 'Private AC car', false],
            ['Sri Lanka Island Escape', 'international', 'Sri Lanka', 6, 5, 105000, null, 'couple', 'all', 'srilanka', ['beach'], 'Kandy, Nuwara Eliya scenic train, Bentota beach and Colombo.', '3-star hotels', 'Breakfast', 'Private car', false],
            ['Australia East Coast', 'international', 'Australia', 9, 8, 520000, null, 'family', 'all', 'australia', ['beach'], 'Sydney Opera House, Blue Mountains, Gold Coast theme parks and Brisbane.', '4-star hotels', 'Breakfast', 'Domestic flights', false],
            // Trekking
            ['Everest Base Camp Trek', 'trekking', 'Everest Region', 14, 13, 145000, 135000, 'adventure', 'autumn', 'ama_dablam', ['everest', 'camping'], 'The classic trek to the foot of the world\'s highest peak via Namche Bazaar, Tengboche and Kala Patthar.', 'Teahouses', 'All meals on trek', 'Kathmandu–Lukla flights', true],
            ['Annapurna Base Camp (ABC) Trek', 'trekking', 'Annapurna', 10, 9, 85000, 79000, 'adventure', 'spring', 'pokhara', ['sunrise'], 'Walk through rhododendron forests and Gurung villages into the Annapurna Sanctuary.', 'Teahouses', 'All meals on trek', 'Tourist bus + jeep', true],
            ['Annapurna Circuit Trek', 'trekking', 'Annapurna', 15, 14, 125000, null, 'adventure', 'autumn', 'mustang', ['pokhara'], 'Cross the Thorong La pass (5,416 m) on one of the world\'s great long-distance treks.', 'Teahouses', 'All meals on trek', 'Jeep + flight', false],
            ['Langtang Valley Trek', 'trekking', 'Langtang', 8, 7, 62000, null, 'adventure', 'spring', 'langtang', ['rara'], 'Close to Kathmandu yet wild — glaciers, yak pastures and Kyanjin Gompa.', 'Teahouses', 'All meals on trek', 'Private jeep', false],
            ['Mardi Himal Trek', 'trekking', 'Annapurna', 6, 5, 48000, null, 'adventure', 'autumn', 'sunrise', ['pokhara'], 'A short, less-crowded ridge trek with face-to-face views of Machhapuchhre.', 'Teahouses', 'All meals on trek', 'Private car', false],
            ['Manaslu Circuit Trek', 'trekking', 'Manaslu', 14, 13, 155000, null, 'adventure', 'autumn', 'mountains', ['ama_dablam'], 'Restricted-area trek around the world\'s eighth highest mountain.', 'Teahouses', 'All meals on trek', 'Jeep', false],
            // Adventure
            ['Everest Helicopter Tour', 'adventure', 'Everest Region', 1, 0, 150000, null, 'luxury', 'all', 'everest', ['ama_dablam'], 'Land at Kala Patthar with breakfast at Hotel Everest View — Everest in a single morning.', null, 'Breakfast', 'Helicopter', true],
            ['Pokhara Paragliding', 'adventure', 'Pokhara', 1, 0, 9500, 8500, 'adventure', 'all', 'viewpoint', ['phewa'], 'Tandem paragliding from Sarangkot over Phewa Lake with GoPro photos and video.', null, null, 'Pickup & drop', true],
            ['Trishuli River Rafting', 'adventure', 'Trishuli', 1, 0, 4500, null, 'group', 'all', 'rafting', ['camping'], 'Grade III white-water rafting day trip with lunch on the riverbank.', null, 'Lunch', 'Tourist bus', true],
            ['Bhote Koshi Bungee Jump', 'adventure', 'Bhote Koshi', 1, 0, 12500, null, 'adventure', 'all', 'langtang', ['rafting'], 'Jump 160 m from a suspension bridge over the Bhote Koshi gorge.', null, 'Lunch', 'Private bus', false],
        ];

        foreach ($tours as $i => [$title, $category, $destination, $days, $nights, $price, $sale, $style, $season, $img, $extraImgs, $summary, $hotel, $meals, $transport, $featured]) {
            $itinerary = [];
            for ($d = 1; $d <= min($days, 14); $d++) {
                $itinerary[] = [
                    'title' => match (true) {
                        $d === 1 => "Arrival & welcome — {$destination}",
                        $d === $days => 'Departure / return to Kathmandu',
                        default => "Day {$d}: Explore {$destination}",
                    },
                    'description' => match (true) {
                        $d === 1 => 'Meet our representative, transfer to the hotel, trip briefing and free time to relax.',
                        $d === $days => 'Breakfast, check-out and transfer. Tour ends with sweet memories.',
                        default => 'Full day of guided sightseeing and activities as per the program, with plenty of time for photos.',
                    },
                ];
            }

            TourPackage::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'category' => $category,
                'destination' => $destination,
                'country' => $category === 'international' ? $destination : 'Nepal',
                'duration_days' => $days,
                'duration_nights' => $nights,
                'price' => $price,
                'sale_price' => $sale,
                'trip_style' => $style,
                'season' => $season,
                'difficulty' => $category === 'trekking' ? ($days > 12 ? 'Strenuous' : 'Moderate') : null,
                'max_altitude' => match ($title) {
                    'Everest Base Camp Trek' => '5,545 m', 'Annapurna Circuit Trek' => '5,416 m',
                    'Annapurna Base Camp (ABC) Trek' => '4,130 m', 'Langtang Valley Trek' => '4,773 m',
                    'Mardi Himal Trek' => '4,500 m', 'Manaslu Circuit Trek' => '5,106 m', default => null,
                },
                'group_size' => '2 – 16',
                'hotel' => $hotel,
                'meals' => $meals,
                'transport' => $transport,
                'summary' => $summary,
                'overview' => '<p>'.$summary.'</p><p>This package is fully customizable — dates, hotel category and activities can be tailored to your group. Our travel consultants will confirm availability and send you a detailed itinerary within 24 hours of booking.</p>',
                'highlights' => array_slice(array_map('trim', preg_split('/[,—]/', $summary)), 0, 5),
                'itinerary' => $itinerary,
                'includes' => $standardIncludes,
                'excludes' => $category === 'international' ? array_merge($standardExcludes, ['International airfare (can be booked separately)']) : $standardExcludes,
                'visa_info' => $category === 'international'
                    ? "Nepali passport holders require a visa for {$destination}. Ram Tours handles the complete visa documentation — see our Visa Services page for the checklist and processing time."
                    : null,
                'map_embed_url' => 'https://www.google.com/maps?q='.urlencode($destination.($category === 'international' ? '' : ', Nepal')).'&output=embed',
                'images' => array_merge([$this->img($img)], array_map(fn ($k) => $this->img($k), $extraImgs)),
                'rating' => [4.7, 4.8, 4.9, 5.0][$i % 4],
                'review_count' => 20 + ($i * 7) % 90,
                'is_featured' => $featured,
                'is_active' => true,
                'position' => $i + 1,
            ]);
        }
    }

    protected function vehicles(): void
    {
        $vehicles = [
            ['Toyota Land Cruiser Prado', 'suv', 7, 4, 'Automatic', 'Diesel', 18000, 2500, false, 'car', true],
            ['Hyundai Creta', 'suv', 5, 3, 'Automatic', 'Petrol', 9000, 2000, true, 'car', true],
            ['Mahindra Scorpio', 'scorpio', 7, 3, 'Manual', 'Diesel', 9500, 2000, false, 'car', true],
            ['Mahindra Bolero Jeep (4WD)', 'jeep', 8, 4, 'Manual', 'Diesel', 11000, 2000, false, 'car', false],
            ['Toyota Hiace (14 Seater)', 'hiace', 14, 10, 'Manual', 'Diesel', 16000, 2500, false, 'bus', true],
            ['Tata Nexon EV', 'ev', 5, 3, 'Automatic', 'Electric', 7500, 2000, true, 'car', true],
            ['BYD Atto 3 EV', 'ev', 5, 3, 'Automatic', 'Electric', 9000, 2000, true, 'car', false],
            ['Mercedes-Benz E-Class (Wedding & VIP)', 'luxury', 4, 2, 'Automatic', 'Petrol', 35000, 3000, false, 'luxury_car', true],
            ['Suzuki Dzire', 'sedan', 4, 2, 'Manual', 'Petrol', 5500, 1800, true, 'car', false],
            ['Toyota Coaster (26 Seater)', 'coaster', 26, 20, 'Manual', 'Diesel', 25000, 3000, false, 'bus', false],
        ];

        foreach ($vehicles as [$name, $category, $seats, $luggage, $transmission, $fuel, $price, $driver, $selfDrive, $img, $featured]) {
            Vehicle::updateOrCreate(['slug' => Str::slug($name)], [
                'name' => $name,
                'category' => $category,
                'seats' => $seats,
                'luggage' => $luggage,
                'transmission' => $transmission,
                'fuel' => $fuel,
                'price_per_day' => $price,
                'driver_charge_per_day' => $driver,
                'self_drive' => $selfDrive,
                'with_driver' => true,
                'services' => array_values(array_filter([
                    'Airport Pickup & Drop', 'Corporate Rental', 'Tour & Sightseeing',
                    $category === 'luxury' ? 'Wedding Car' : null,
                    $selfDrive ? 'Self Drive' : null,
                ])),
                'features' => array_values(array_filter(['Air Conditioning', 'Bluetooth Audio', 'Comprehensive Insurance', 'GPS Tracking', $category === 'ev' ? 'Free Charging Cable' : 'Fuel Efficient', 'Professional Driver'])),
                'images' => [$this->img($img)],
                'description' => "Rent the {$name} in Kathmandu for city tours, airport transfers, out-of-valley trips or corporate travel. Well-maintained, insured and available with an experienced, licensed driver".($selfDrive ? ' or as a self-drive rental.' : '.'),
                'is_featured' => $featured,
                'is_active' => true,
            ]);
        }
    }

    protected function busRoutes(): void
    {
        BusRoute::query()->delete();

        $routes = [
            ['Kathmandu', 'Pokhara', 'Greenline Tours', 'Greenline Deluxe', 'ac', '07:00', '14:00', 2200, 30],
            ['Kathmandu', 'Pokhara', 'Jagadamba Travels', 'Jagadamba Sofa Bus', 'sofa', '07:30', '14:30', 1800, 29],
            ['Kathmandu', 'Pokhara', 'Mountain Overland', 'Tourist Bus', 'tourist', '06:30', '14:00', 1200, 35],
            ['Pokhara', 'Kathmandu', 'Greenline Tours', 'Greenline Deluxe', 'ac', '07:00', '14:00', 2200, 30],
            ['Pokhara', 'Kathmandu', 'Mountain Overland', 'Tourist Bus', 'tourist', '07:00', '14:30', 1200, 35],
            ['Kathmandu', 'Chitwan', 'Blue Sky Travels', 'Blue Sky Tourist', 'tourist', '07:00', '12:30', 1000, 35],
            ['Kathmandu', 'Chitwan', 'Greenline Tours', 'Greenline Deluxe', 'ac', '07:30', '12:30', 1800, 30],
            ['Chitwan', 'Kathmandu', 'Blue Sky Travels', 'Blue Sky Tourist', 'tourist', '09:30', '15:00', 1000, 35],
            ['Kathmandu', 'Butwal', 'Buddha Yatayat', 'Deluxe Day Bus', 'deluxe', '07:00', '15:00', 1300, 35],
            ['Kathmandu', 'Lumbini', 'Lumbini Deluxe', 'Lumbini VIP', 'vip', '19:00', '05:30', 1800, 29],
            ['Kathmandu', 'Dharan', 'Sajha Yatayat', 'Night Deluxe', 'night', '17:30', '05:00', 1700, 35],
            ['Kathmandu', 'Nepalgunj', 'Bheri Yatayat', 'Night Sofa Bus', 'night', '15:00', '04:30', 2100, 29],
            ['Kathmandu', 'Biratnagar', 'Sajha Yatayat', 'VIP Sofa', 'vip', '16:30', '05:30', 2300, 29],
            ['Kathmandu', 'Janakpur', 'Mithila Yatayat', 'Deluxe Night', 'night', '18:00', '04:00', 1500, 35],
        ];

        foreach ($routes as [$from, $to, $operator, $name, $type, $dep, $arr, $price, $seats]) {
            BusRoute::create([
                'operator' => $operator,
                'bus_name' => $name,
                'bus_type' => $type,
                'from_city' => $from,
                'to_city' => $to,
                'departure_time' => $dep,
                'arrival_time' => $arr,
                'boarding_point' => $from === 'Kathmandu' ? 'Kantipath / Sorhakhutte, Kathmandu' : "{$from} Tourist Bus Park",
                'dropping_point' => "{$to} Tourist Bus Park",
                'price' => $price,
                'total_seats' => $seats,
                'amenities' => array_values(array_filter([
                    in_array($type, ['ac', 'vip', 'sofa'], true) ? 'Air Conditioning' : null,
                    'Wi-Fi', 'Charging Point', in_array($type, ['ac', 'sofa', 'vip'], true) ? 'Lunch / Snacks' : 'Water Bottle',
                    in_array($type, ['sofa', 'vip', 'night'], true) ? 'Reclining Sofa Seats' : 'Pushback Seats',
                    'Blanket', 'CCTV',
                ])),
                'is_active' => true,
            ]);
        }
    }

    protected function visas(): void
    {
        $common = ['Original passport (valid 6+ months) with 2 blank pages', '2 recent passport-size photos (white background)', 'Citizenship certificate copy', 'Bank statement (last 6 months)', 'Bank balance certificate'];

        $visas = [
            ['United Arab Emirates', '🇦🇪', 'tourist', '3 – 5 working days', '60 days', '30 days', 14500, 2000, 'dubai', ['Confirmed return air ticket', 'Hotel booking'], true],
            ['Thailand', '🇹🇭', 'tourist', '5 – 7 working days', '3 months', '60 days', 6500, 1500, 'thailand', ['Confirmed air ticket', 'Hotel booking', 'Employment letter / business registration'], true],
            ['Malaysia', '🇲🇾', 'tourist', '3 – 5 working days', '3 months', '30 days', 5500, 1500, 'malaysia', ['Confirmed air ticket', 'Hotel booking'], true],
            ['Singapore', '🇸🇬', 'tourist', '5 – 7 working days', '2 years (multiple)', '30 days', 5000, 2000, 'singapore', ['Confirmed air ticket', 'Hotel booking', 'Form 14A'], true],
            ['Japan', '🇯🇵', 'visit', '7 – 10 working days', '3 months', '15 days', 0, 4000, 'japan', ['Invitation / guarantee letter', 'Detailed itinerary', 'Tax clearance'], false],
            ['South Korea', '🇰🇷', 'work', 'As per EPS schedule', '4 years 10 months', 'Employment period', 0, 5000, 'korea', ['EPS-TOPIK result', 'Standard labor contract', 'Medical report'], false],
            ['Australia', '🇦🇺', 'student', '4 – 8 weeks', 'Course duration', 'Course duration', 105000, 25000, 'australia', ['CoE from institution', 'IELTS / PTE score', 'GTE statement', 'Sponsor documents', 'OSHC insurance'], true],
            ['United Kingdom', '🇬🇧', 'student', '3 – 6 weeks', 'Course duration', 'Course duration', 70000, 25000, 'europe', ['CAS letter', 'IELTS UKVI score', 'TB test certificate', 'Maintenance funds (28 days)'], false],
            ['Schengen (Europe)', '🇪🇺', 'tourist', '15 – 30 working days', 'Up to 90 days', '90 days', 13000, 5000, 'europe_lake', ['Travel insurance (EUR 30,000)', 'Flight reservation', 'Hotel booking', 'Cover letter & itinerary', 'ITR / tax documents'], true],
            ['India', '🇮🇳', 'business', '3 – 5 working days', '1 year', '180 days', 4000, 1500, 'india', ['Business invitation letter', 'Company registration'], false],
            ['USA', '🇺🇸', 'visit', 'As per interview date', 'Up to 10 years', 'Up to 6 months', 25000, 7000, 'usa', ['DS-160 confirmation', 'Interview appointment letter', 'Property valuation', 'Employment / business documents'], false],
            ['Qatar', '🇶🇦', 'work', '2 – 4 weeks', '2 years', 'Employment period', 0, 5000, 'dubai', ['Job offer letter', 'Labor permit', 'Medical report', 'Police report'], false],
        ];

        foreach ($visas as [$country, $flag, $type, $processing, $validity, $stay, $embassy, $service, $img, $extra, $featured]) {
            $title = $country.' '.config('travel.visa_types.'.$type);

            VisaService::updateOrCreate(['slug' => Str::slug($title)], [
                'country' => $country,
                'flag' => $flag,
                'visa_type' => $type,
                'title' => $title,
                'processing_time' => $processing,
                'validity' => $validity,
                'stay_duration' => $stay,
                'embassy_fee' => $embassy,
                'service_charge' => $service,
                'requirements' => array_merge($common, $extra),
                'description' => "<p>Ram Tours & Travel provides complete {$title} assistance for Nepali passport holders — document checklist, form filling, appointment booking, submission and follow-up. Share your documents online and our visa desk will review them within one working day.</p>",
                'image' => $this->img($img),
                'is_featured' => $featured,
                'is_active' => true,
            ]);
        }
    }

    protected function testimonials(): void
    {
        $reviews = [
            ['Sujata Sharma', 'Kathmandu', 'avatar_1', 'Ram Tours made our Dubai trip unforgettable. Excellent service, well-organized itinerary and very supportive team. Highly recommended!', 'google'],
            ['Rajesh Thapa', 'Pokhara', 'avatar_2', 'Booked our family Chitwan safari and Greenline bus tickets together. Everything was on time and the jungle resort was amazing.', 'facebook'],
            ['Emily Clarke', 'London, UK', 'avatar_3', 'Our Everest Base Camp trek guide was fantastic — safe, knowledgeable and so kind. Ram Tours handled every permit and flight for us.', 'google'],
            ['Anil Gurung', 'Lalitpur', 'avatar_4', 'Got my Schengen visa processed without any stress. The visa team checked every document and kept me updated daily.', 'website'],
            ['Priya Karki', 'Bhaktapur', 'avatar_1', 'Best price for our Bali honeymoon package and the private pool villa was a dream. Thank you Ram Tours!', 'facebook'],
        ];

        foreach ($reviews as $i => [$name, $location, $avatar, $content, $source]) {
            Testimonial::updateOrCreate(['name' => $name], [
                'location' => $location,
                'avatar' => $this->img($avatar),
                'rating' => 5,
                'content' => $content,
                'source' => $source,
                'is_approved' => true,
                'position' => $i + 1,
            ]);
        }
    }

    protected function blog(): void
    {
        $author = User::where('role', 'super_admin')->first();

        $posts = [
            ['Everest Base Camp Trek: The Complete 2026 Guide', 'Trekking', 'ama_dablam', 'Best season, permits, cost breakdown, altitude tips and packing list for the world\'s most famous trek.'],
            ['Dubai Visa for Nepali Citizens: Documents & Processing Time', 'Visa Updates', 'dubai', 'Everything you need to apply for a UAE tourist visa from Nepal, including the latest fees and checklist.'],
            ['Top 10 Things to Do in Pokhara', 'Destination Guide', 'pokhara', 'From paragliding over Phewa Lake to sunrise at Sarangkot — the Pokhara bucket list.'],
            ['Dashain & Tihar Travel Tips: Beat the Festival Rush', 'Festival Travel', 'kathmandu', 'How to book bus and flight tickets early, avoid price surges and travel home safely this festive season.'],
            ['Domestic Flight Baggage Rules in Nepal', 'Airline News', 'plane_landing', 'Buddha Air, Yeti and Shree Airlines baggage allowances, check-in times and what you can carry.'],
            ['International Travel Checklist Before You Fly', 'Travel Checklist', 'traveler', 'Passport validity, travel insurance, forex, SIM cards and the documents to keep handy at immigration.'],
        ];

        foreach ($posts as $i => [$title, $category, $img, $excerpt]) {
            BlogPost::updateOrCreate(['slug' => Str::slug($title)], [
                'user_id' => $author?->id,
                'title' => $title,
                'category' => $category,
                'excerpt' => $excerpt,
                'content' => "<p>{$excerpt}</p><h2>Plan ahead</h2><p>Whether you are travelling within Nepal or abroad, planning a few weeks in advance gives you the best prices and availability. Our consultants at Ram Tours & Travel can help you compare options and put together the right package.</p><h2>Our top tips</h2><ul><li>Book flights and buses early during peak seasons and festivals.</li><li>Keep digital and printed copies of your passport, visa and tickets.</li><li>Buy travel insurance that covers your activities.</li><li>Carry some cash in local currency for small purchases.</li></ul><h2>Need help?</h2><p>Call us or drop by our office in Pabitra Nagar, Gangabu, Kathmandu — we are happy to help you plan every detail.</p>",
                'featured_image' => $this->img($img),
                'meta_description' => $excerpt,
                'published_at' => now()->subDays(($i + 1) * 4),
                'views' => 120 + $i * 37,
            ]);
        }
    }

    protected function offers(): void
    {
        $offers = [
            ['Dashain Mega Offer', 'DASHAIN15', 'Celebrate Dashain with 15% off on all Nepal tour packages.', 'percent', 15, 10000, 10000, 'tour', 'Festival', 'pokhara', 45],
            ['Tihar Family Getaway', 'TIHAR10', 'Flat 10% off on domestic packages booked for Tihar holidays.', 'percent', 10, 15000, 8000, 'tour', 'Festival', 'kathmandu', 60],
            ['Early Bird International', 'EARLYBIRD', 'Book any international holiday 45 days in advance and save Rs. 7,500.', 'fixed', 7500, 80000, null, 'tour', 'Early Bird', 'maldives', 90],
            ['New Year Car Rental Deal', 'NEWYEAR5', '5% off on all car and jeep rentals.', 'percent', 5, 5000, 5000, 'car', 'New Year', 'car', 100],
            ['Summer Bus Saver', 'BUS100', 'Rs. 100 off on bus tickets above Rs. 1,500.', 'fixed', 100, 1500, null, 'bus', 'Summer', 'bus', 30],
        ];

        foreach ($offers as [$title, $code, $desc, $type, $value, $min, $max, $applies, $badge, $img, $days]) {
            Offer::updateOrCreate(['code' => $code], [
                'title' => $title,
                'description' => $desc,
                'discount_type' => $type,
                'discount_value' => $value,
                'min_amount' => $min,
                'max_discount' => $max,
                'applies_to' => $applies,
                'badge' => $badge,
                'image' => $this->img($img),
                'link_url' => match ($applies) { 'car' => '/car-rental', 'bus' => '/bus-tickets', default => '/tours' },
                'starts_at' => now()->subDay(),
                'expires_at' => now()->addDays($days),
                'is_active' => true,
            ]);
        }
    }

    protected function faqs(): void
    {
        $faqs = [
            ['General', 'Is Ram Tours & Travel a registered company?', 'Yes. Ram Tours & Travel Pvt. Ltd. is registered with the Government of Nepal and affiliated with the Nepal Tourism Board and NATA.'],
            ['General', 'Where is your office located?', 'Our office is in Pabitra Nagar, Gangabu, Kathmandu. We are open Sunday to Friday, 9 AM – 6 PM, and Saturday 10 AM – 2 PM.'],
            ['Tour Packages', 'Can I customize a tour package?', 'Absolutely. Every package can be tailored — dates, hotels, activities and group size. Send a booking or quote request and a consultant will contact you.'],
            ['Tour Packages', 'Do prices include international flights?', 'International package prices are land-only unless stated. We can book your flights separately at the best available fare.'],
            ['Visa', 'How long does visa processing take?', 'It depends on the country — from 3 working days (UAE, Malaysia) to several weeks (Schengen, Australia). Each visa page lists the typical processing time.'],
            ['Visa', 'Can I upload my visa documents online?', 'Yes. Use the Apply Now form on any visa page to upload scanned documents; our visa desk reviews them within one working day.'],
            ['Flight Tickets', 'How do I get my e-ticket?', 'Once your fare is confirmed and paid, your e-ticket is emailed to you and also available on your booking confirmation page.'],
            ['Flight Tickets', 'Can I change my flight date?', 'Yes, submit a Ticket Reissue request with your PNR. Airline change fees and fare differences may apply.'],
            ['Bus Tickets', 'Can I choose my bus seat?', 'Yes — pick your exact seats on the interactive seat map before you pay. Booked seats are shown in grey.'],
            ['Bus Tickets', 'What should I show when boarding?', 'Show your booking ID or the printed / mobile ticket with the QR code to the bus conductor.'],
            ['Payments & Refunds', 'Which payment methods do you accept?', 'eSewa, Khalti, FonePay, ConnectIPS, IME Pay, Visa/MasterCard, PayPal, bank transfer or cash at our office.'],
            ['Payments & Refunds', 'What is your refund policy?', 'Refunds follow the supplier\'s cancellation terms (airline, bus operator, hotel). Our service charge is non-refundable once services are issued. Submit a refund request and we will process it within 7–14 working days.'],
        ];

        Faq::query()->delete();

        foreach ($faqs as $i => [$category, $question, $answer]) {
            Faq::create(['category' => $category, 'question' => $question, 'answer' => $answer, 'position' => $i + 1, 'is_active' => true]);
        }
    }

    protected function gallery(): void
    {
        GalleryItem::query()->delete();

        $items = [
            ['Everest Region', 'Trekking', 'ama_dablam'], ['Boudhanath Stupa', 'Destinations', 'boudhanath'],
            ['Phewa Lake, Pokhara', 'Destinations', 'phewa'], ['Chitwan Safari', 'Destinations', 'chitwan'],
            ['Annapurna Sunrise', 'Trekking', 'sunrise'], ['Our Trekking Group', 'Customer Memories', 'group'],
            ['Dubai Skyline', 'International', 'dubai'], ['Maldives', 'International', 'maldives'],
            ['Swayambhunath', 'Destinations', 'swayambhu'], ['Trishuli Rafting', 'Customer Memories', 'rafting'],
            ['Bali Temple', 'International', 'bali'], ['Himalayan Camp', 'Trekking', 'camping'],
        ];

        foreach ($items as $i => [$title, $album, $img]) {
            GalleryItem::create(['title' => $title, 'album' => $album, 'type' => 'image', 'path' => $this->img($img), 'position' => $i + 1]);
        }

        GalleryItem::create(['title' => 'Discover Nepal', 'album' => 'Videos', 'type' => 'video', 'path' => 'https://www.youtube.com/watch?v=Z8B7ZyvD7Lo', 'position' => 20]);
    }
}
