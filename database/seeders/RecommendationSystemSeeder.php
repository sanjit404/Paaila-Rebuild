<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\TourPackage;
use App\Models\Checkpoint;
use App\Models\CheckpointFact;
use App\Models\TourBooking;
use App\Models\TrekRating;
use App\Models\UserPreference;
use App\Models\TrackingPin;
use App\Models\CheckpointProgress;
use Carbon\Carbon;

class RecommendationSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Recommendation System...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->seedTourPackages();
        $this->seedCheckpoints();
        $this->seedUsers();
        $this->seedUserPreferences();
        $this->seedBookingsAndRatings();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Recommendation System seeded successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | TOUR PACKAGES
    | Covers all 8 trek types used by RecommendationService.
    | Each has trek_type, tags, season, region, difficulty, duration, price.
    |--------------------------------------------------------------------------
    */
    private function seedTourPackages(): void
{
    $this->command->info('  → Seeding tour packages...');

    $packages = [

        // ── NATURE ────────────────────────────────────────────────────
        [
            'name' => 'Shivapuri Forest Day Trek',
            'description' => 'A refreshing full-day hike through the dense Shivapuri National Park forest on the northern rim of Kathmandu Valley. Spot wildlife, hear birds, and breathe clean mountain air.',
            'trek_type' => 'nature',
            'tags' => json_encode(['forest', 'wildlife', 'birdwatching', 'day-trek', 'kathmandu']),
            'season' => json_encode(['spring', 'autumn', 'winter']),
            'region' => 'Kathmandu Valley',
            'difficulty_level' => 'easy',
            'duration_days' => 1,
            'max_participants' => 20,
            'price' => 2500.00,

            'start_location_name' => 'Budhanilkantha',
            'end_location_name' => 'Shivapuri Peak',

            'start_lat' => 27.7690,
            'start_lng' => 85.3480,
            'end_lat' => 27.7890,
            'end_lng' => 85.3600,

            'is_active' => true,
            'rating_avg' => 4.30,
            'rating_count' => 12,
            'bookings_count' => 28,
            'views_count' => 145,
        ],

        [
            'name' => 'Chitwan Jungle Safari & Village Trek',
            'description' => 'Two days deep inside Chitwan National Park. Elephant safaris at dawn, canoe rides on the Rapti River, and an evening with the Tharu community.',
            'trek_type' => 'nature',
            'tags' => json_encode(['wildlife', 'safari', 'jungle', 'river', 'tharu', 'elephant']),
            'season' => json_encode(['autumn', 'winter', 'spring']),
            'region' => 'Chitwan',
            'difficulty_level' => 'easy',
            'duration_days' => 2,
            'max_participants' => 15,
            'price' => 6500.00,

            'start_location_name' => 'Sauraha',
            'end_location_name' => 'Chitwan National Park',

            'start_lat' => 27.5291,
            'start_lng' => 84.3542,
            'end_lat' => 27.5100,
            'end_lng' => 84.3300,

            'is_active' => true,
            'rating_avg' => 4.60,
            'rating_count' => 19,
            'bookings_count' => 41,
            'views_count' => 210,
        ],

        // ── HISTORICAL ────────────────────────────────────────────────
        [
            'name' => 'Kathmandu Heritage Walking Tour',
            'description' => 'Walk through 2,500 years of Nepalese history. Pashupatinath, Boudhanath, Swayambhunath, and Kathmandu Durbar Square — all in one carefully guided day.',
            'trek_type' => 'historical',
            'tags' => json_encode(['heritage', 'temples', 'UNESCO', 'ancient', 'stupa', 'history']),
            'season' => json_encode(['spring', 'autumn', 'winter', 'summer']),
            'region' => 'Kathmandu Valley',
            'difficulty_level' => 'easy',
            'duration_days' => 1,
            'max_participants' => 25,
            'price' => 3000.00,

            'start_location_name' => 'Pashupatinath Temple',
            'end_location_name' => 'Kathmandu Durbar Square',

            'start_lat' => 27.7104,
            'start_lng' => 85.3484,
            'end_lat' => 27.7149,
            'end_lng' => 85.2903,

            'is_active' => true,
            'rating_avg' => 4.70,
            'rating_count' => 31,
            'bookings_count' => 67,
            'views_count' => 320,
        ],

        [
            'name' => 'Bhaktapur & Patan Ancient Cities Walk',
            'description' => 'Explore the medieval masterpieces of Bhaktapur Durbar Square and Patan\'s golden temples.',
            'trek_type' => 'historical',
            'tags' => json_encode(['newar', 'medieval', 'durbar', 'architecture', 'pottery', 'heritage']),
            'season' => json_encode(['spring', 'autumn', 'winter']),
            'region' => 'Kathmandu Valley',
            'difficulty_level' => 'easy',
            'duration_days' => 2,
            'max_participants' => 20,
            'price' => 4500.00,

            'start_location_name' => 'Bhaktapur Durbar Square',
            'end_location_name' => 'Patan Durbar Square',

            'start_lat' => 27.6710,
            'start_lng' => 85.4298,
            'end_lat' => 27.6588,
            'end_lng' => 85.3247,

            'is_active' => true,
            'rating_avg' => 4.50,
            'rating_count' => 18,
            'bookings_count' => 39,
            'views_count' => 187,
        ],

        // ── CULTURAL ──────────────────────────────────────────────────
        [
            'name' => 'Newari Village Cultural Immersion',
            'description' => 'Stay with a Newari family for three days.',
            'trek_type' => 'cultural',
            'tags' => json_encode(['newari', 'village', 'cooking', 'festival', 'homestay', 'tradition']),
            'season' => json_encode(['spring', 'autumn']),
            'region' => 'Kathmandu Valley',
            'difficulty_level' => 'easy',
            'duration_days' => 3,
            'max_participants' => 10,
            'price' => 8500.00,

            'start_location_name' => 'Kirtipur',
            'end_location_name' => 'Newari Heritage Village',

            'start_lat' => 27.6905,
            'start_lng' => 85.3200,
            'end_lat' => 27.7000,
            'end_lng' => 85.3150,

            'is_active' => true,
            'rating_avg' => 4.80,
            'rating_count' => 14,
            'bookings_count' => 22,
            'views_count' => 134,
        ],

        [
            'name' => 'Gurung Highland Culture Trek',
            'description' => 'Trek through Gurung villages in the Annapurna foothills.',
            'trek_type' => 'cultural',
            'tags' => json_encode(['gurung', 'highland', 'village', 'tradition', 'annapurna', 'community']),
            'season' => json_encode(['spring', 'autumn']),
            'region' => 'Pokhara',
            'difficulty_level' => 'moderate',
            'duration_days' => 5,
            'max_participants' => 12,
            'price' => 12000.00,

            'start_location_name' => 'Pokhara',
            'end_location_name' => 'Ghandruk Village',

            'start_lat' => 28.2096,
            'start_lng' => 83.9856,
            'end_lat' => 28.2500,
            'end_lng' => 84.0200,

            'is_active' => true,
            'rating_avg' => 4.40,
            'rating_count' => 9,
            'bookings_count' => 17,
            'views_count' => 98,
        ],

        // ── ADVENTURE ─────────────────────────────────────────────────
        [
            'name' => 'Langtang Valley Trek',
            'description' => 'Seven days through the stunning Langtang Valley.',
            'trek_type' => 'adventure',
            'tags' => json_encode(['glacier', 'high-altitude', 'tamang', 'valley', 'monastery', 'himalaya']),
            'season' => json_encode(['spring', 'autumn']),
            'region' => 'Langtang',
            'difficulty_level' => 'moderate',
            'duration_days' => 7,
            'max_participants' => 14,
            'price' => 18000.00,

            'start_location_name' => 'Syabrubesi',
            'end_location_name' => 'Kyanjin Gompa',

            'start_lat' => 28.2135,
            'start_lng' => 85.5145,
            'end_lat' => 28.2200,
            'end_lng' => 85.5500,

            'is_active' => true,
            'rating_avg' => 4.75,
            'rating_count' => 24,
            'bookings_count' => 35,
            'views_count' => 278,
        ],

        [
            'name' => 'Mardi Himal Base Camp',
            'description' => 'A challenging 5-day push to the remote Mardi Himal base camp at 4,500m.',
            'trek_type' => 'adventure',
            'tags' => json_encode(['base-camp', 'high-altitude', 'annapurna', 'remote', 'panorama', 'peak']),
            'season' => json_encode(['spring', 'autumn']),
            'region' => 'Pokhara',
            'difficulty_level' => 'hard',
            'duration_days' => 5,
            'max_participants' => 10,
            'price' => 22000.00,

            'start_location_name' => 'Kande',
            'end_location_name' => 'Mardi Himal Base Camp',

            'start_lat' => 28.3200,
            'start_lng' => 83.9500,
            'end_lat' => 28.4100,
            'end_lng' => 83.9700,

            'is_active' => true,
            'rating_avg' => 4.90,
            'rating_count' => 11,
            'bookings_count' => 19,
            'views_count' => 203,
        ],

        // ── SPIRITUAL ─────────────────────────────────────────────────
        [
            'name' => 'Muktinath Pilgrimage Trek',
            'description' => 'Follow the ancient pilgrimage trail to Muktinath temple.',
            'trek_type' => 'spiritual',
            'tags' => json_encode(['pilgrimage', 'temple', 'mustang', 'sacred', 'buddhist', 'hindu']),
            'season' => json_encode(['spring', 'summer', 'autumn']),
            'region' => 'Mustang',
            'difficulty_level' => 'moderate',
            'duration_days' => 6,
            'max_participants' => 15,
            'price' => 16000.00,

            'start_location_name' => 'Jomsom',
            'end_location_name' => 'Muktinath Temple',

            'start_lat' => 28.8167,
            'start_lng' => 83.8667,
            'end_lat' => 28.8314,
            'end_lng' => 83.8697,

            'is_active' => true,
            'rating_avg' => 4.65,
            'rating_count' => 16,
            'bookings_count' => 28,
            'views_count' => 198,
        ],

        // ── SCENIC ────────────────────────────────────────────────────
        [
            'name' => 'Sarangkot Sunrise & Pokhara Valley Trek',
            'description' => 'Start before dawn and reach Sarangkot for the most famous sunrise view in Nepal.',
            'trek_type' => 'scenic',
            'tags' => json_encode(['sunrise', 'viewpoint', 'pokhara', 'annapurna']),
            'season' => json_encode(['spring', 'autumn', 'winter']),
            'region' => 'Pokhara',
            'difficulty_level' => 'easy',
            'duration_days' => 1,
            'max_participants' => 30,
            'price' => 2000.00,

            'start_location_name' => 'Pokhara Lakeside',
            'end_location_name' => 'Sarangkot Viewpoint',

            'start_lat' => 28.2096,
            'start_lng' => 83.9856,
            'end_lat' => 28.2264,
            'end_lng' => 83.9558,

            'is_active' => true,
            'rating_avg' => 4.85,
            'rating_count' => 42,
            'bookings_count' => 89,
            'views_count' => 415,
        ],

        // ── WILDLIFE ──────────────────────────────────────────────────
        [
            'name' => 'Bardia Tiger Safari Trek',
            'description' => 'Nepal\'s best-kept secret for tiger watching.',
            'trek_type' => 'wildlife',
            'tags' => json_encode(['tiger', 'safari', 'jungle', 'national-park']),
            'season' => json_encode(['winter', 'spring']),
            'region' => 'Bardia',
            'difficulty_level' => 'easy',
            'duration_days' => 3,
            'max_participants' => 8,
            'price' => 14000.00,

            'start_location_name' => 'Thakurdwara',
            'end_location_name' => 'Bardia National Park',

            'start_lat' => 28.3167,
            'start_lng' => 81.4333,
            'end_lat' => 28.3500,
            'end_lng' => 81.4600,

            'is_active' => true,
            'rating_avg' => 4.55,
            'rating_count' => 8,
            'bookings_count' => 13,
            'views_count' => 112,
        ],

        // ── VILLAGE ───────────────────────────────────────────────────
        [
            'name' => 'Nuwakot Village & Fort Trek',
            'description' => 'A peaceful 3-day trek to the hilltop fortress town of Nuwakot.',
            'trek_type' => 'village',
            'tags' => json_encode(['village', 'fort', 'history', 'farming']),
            'season' => json_encode(['spring', 'autumn', 'winter']),
            'region' => 'Nuwakot',
            'difficulty_level' => 'moderate',
            'duration_days' => 3,
            'max_participants' => 12,
            'price' => 7500.00,

            'start_location_name' => 'Bidur',
            'end_location_name' => 'Nuwakot Durbar',

            'start_lat' => 28.0333,
            'start_lng' => 85.1667,
            'end_lat' => 28.0500,
            'end_lng' => 85.1800,

            'is_active' => true,
            'rating_avg' => 4.35,
            'rating_count' => 7,
            'bookings_count' => 14,
            'views_count' => 88,
        ],
    ];

    foreach ($packages as $pkg) {

        TourPackage::updateOrCreate(
            ['name' => $pkg['name']],
            $pkg
        );
    }

    $this->command->info('     ✓ ' . count($packages) . ' tour packages seeded');
}

    /*
    |--------------------------------------------------------------------------
    | CHECKPOINTS
    | Uses exact schema: name, description, order, latitude, longitude,
    | radius, estimated_time_from_previous, image
    |--------------------------------------------------------------------------
    */
    private function seedCheckpoints(): void
    {
        $this->command->info('  → Seeding checkpoints & facts...');

        $checkpointData = [

            'Kathmandu Heritage Walking Tour' => [
                [
                    'name'                        => 'Pashupatinath Temple',
                    'description'           => 'Sacred Hindu temple on the banks of Bagmati River — one of the holiest Shiva shrines in the world.',
                    'order'                       => 1,
                    'latitude'                    => 27.7104,
                    'longitude'                   => 85.3484,
                    'radius'                      => 100,
                    'estimated_time_from_previous'=> 10,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'Age of the Temple',   'content' => 'Built in the 5th century AD, Pashupatinath has been a centre of Shaivism for over 1,500 years.',            'type' => 'history',  'icon_class' => null, 'order' => 1],
                        ['title' => 'Bagmati River',       'content' => 'The Bagmati is considered sacred. Traditional Hindu cremations are performed on the ghats beside the river.', 'type' => 'culture',  'icon_class' => null, 'order' => 2],
                        ['title' => 'Visitor Tips',        'content' => 'Non-Hindus are not permitted inside the main temple. The surrounding ghats and gardens are freely accessible.', 'type' => 'safety',   'icon_class' => null, 'order' => 3],
                    ],
                ],
                [
                    'name'                        => 'Boudhanath Stupa',
                    'description'           => 'One of the largest Buddhist stupas in the world — the centre of Tibetan Buddhism in Nepal.',
                    'order'                       => 2,
                    'latitude'                    => 27.7215,
                    'longitude'                   => 85.3620,
                    'radius'                      => 120,
                    'estimated_time_from_previous'=> 20,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'All-Seeing Eyes',     'content' => 'The painted eyes on all four sides represent the all-seeing wisdom of Buddha, watching over the valley.',      'type' => 'info',     'icon_class' => null, 'order' => 1],
                        ['title' => 'Tibetan Community',   'content' => 'After 1959 thousands of Tibetan refugees settled here. Over 50 monasteries surround the stupa today.',         'type' => 'culture',  'icon_class' => null, 'order' => 2],
                        ['title' => 'Kora Practice',       'content' => 'Walking clockwise around the stupa (kora) is a form of meditation. Each circuit is said to accumulate merit.', 'type' => 'info',     'icon_class' => null, 'order' => 3],
                    ],
                ],
                [
                    'name'                        => 'Swayambhunath Stupa',
                    'description'           => 'The "Monkey Temple" — ancient Buddhist stupa perched atop a hill with panoramic valley views.',
                    'order'                       => 3,
                    'latitude'                    => 27.7149,
                    'longitude'                   => 85.2903,
                    'radius'                      => 100,
                    'estimated_time_from_previous'=> 30,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'Over 2,500 Years Old', 'content' => 'Swayambhunath is one of the oldest religious sites in Nepal, predating the written history of the valley.',   'type' => 'history',  'icon_class' => null, 'order' => 1],
                        ['title' => 'Sacred Monkeys',        'content' => 'Troops of Rhesus macaques live in the surrounding forest. They are considered sacred guardians of the site.',  'type' => 'info',     'icon_class' => null, 'order' => 2],
                    ],
                ],
                [
                    'name'                        => 'Kathmandu Durbar Square',
                    'description'           => 'UNESCO World Heritage site — the ancient palace complex of Kathmandu\'s former royal family.',
                    'order'                       => 4,
                    'latitude'                    => 27.7046,
                    'longitude'                   => 85.3077,
                    'radius'                      => 150,
                    'estimated_time_from_previous'=> 25,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'Living Goddess Kumari', 'content' => 'The Kumari Ghar houses the Kumari — a young girl worshipped as a living goddess until she reaches puberty.', 'type' => 'culture',  'icon_class' => null, 'order' => 1],
                        ['title' => 'Malla Dynasty',         'content' => 'The square was built by the Malla kings between the 12th and 18th centuries.',                               'type' => 'history',  'icon_class' => null, 'order' => 2],
                        ['title' => '2015 Earthquake',       'content' => 'The 2015 earthquake damaged several structures. Reconstruction is ongoing — some areas may be restricted.',   'type' => 'safety',   'icon_class' => null, 'order' => 3],
                    ],
                ],
            ],

            'Langtang Valley Trek' => [
                [
                    'name'                        => 'Syabru Besi',
                    'description'           => 'Trek starting point — a small bazaar village at 1,470m on the banks of the Bhote Koshi River.',
                    'order'                       => 1,
                    'latitude'                    => 28.1580,
                    'longitude'                   => 85.3420,
                    'radius'                      => 100,
                    'estimated_time_from_previous'=> 10,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'Entry Point',    'content' => 'Collect your Langtang National Park entry permit here. Keep it with you throughout the trek.',   'type' => 'safety',  'icon_class' => null, 'order' => 1],
                        ['title' => 'Bhote Koshi',    'content' => 'The river originates in Tibet. "Bhote" means Tibetan — reflecting the deep cross-border cultural links.', 'type' => 'info', 'icon_class' => null, 'order' => 2],
                    ],
                ],
                [
                    'name'                        => 'Lama Hotel',
                    'description'           => 'A cluster of tea houses at 2,380m surrounded by dense rhododendron and oak forest.',
                    'order'                       => 2,
                    'latitude'                    => 28.1780,
                    'longitude'                   => 85.4120,
                    'radius'                      => 100,
                    'estimated_time_from_previous'=> 240,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'Red Panda Territory', 'content' => 'The dense forests between Syabru Besi and Lama Hotel are prime red panda habitat. Dawn and dusk are best for sightings.', 'type' => 'info', 'icon_class' => null, 'order' => 1],
                        ['title' => 'Altitude Note',       'content' => 'You have gained over 900m today. Drink 3–4 litres of water and avoid alcohol tonight.',                                      'type' => 'safety', 'icon_class' => null, 'order' => 2],
                    ],
                ],
                [
                    'name'                        => 'Langtang Village',
                    'description'           => 'Historic Tamang village at 3,430m — largely rebuilt after being destroyed in the 2015 earthquake.',
                    'order'                       => 3,
                    'latitude'                    => 28.2135,
                    'longitude'                   => 85.5145,
                    'radius'                      => 120,
                    'estimated_time_from_previous'=> 300,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => '2015 Earthquake',  'content' => 'An earthquake-triggered avalanche destroyed most of the original village. A memorial honours the 243 people who died.', 'type' => 'history', 'icon_class' => null, 'order' => 1],
                        ['title' => 'Tamang Culture',   'content' => 'The Tamang people of Langtang are descendants of Tibetan nomads. Their Buddhist traditions are alive in every home.', 'type' => 'culture', 'icon_class' => null, 'order' => 2],
                        ['title' => 'Yak Cheese',       'content' => 'Try the locally produced yak cheese — Langtang is famous for it. The cheese factory is open to visitors.',             'type' => 'info',    'icon_class' => null, 'order' => 3],
                    ],
                ],
                [
                    'name'                        => 'Kyanjin Gompa',
                    'description'           => 'Ancient Buddhist monastery at 3,870m — the spiritual heart of the Langtang Valley.',
                    'order'                       => 4,
                    'latitude'                    => 28.2135,
                    'longitude'                   => 85.5630,
                    'radius'                      => 150,
                    'estimated_time_from_previous'=> 90,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'Monastery History',  'content' => 'Kyanjin Gompa was built several centuries ago and is believed to have been founded by a Tibetan lama.', 'type' => 'history', 'icon_class' => null, 'order' => 1],
                        ['title' => 'Altitude Warning',   'content' => 'At 3,870m altitude sickness is possible. Rest for at least one full day here before ascending higher.', 'type' => 'safety',  'icon_class' => null, 'order' => 2],
                        ['title' => 'Side Trip',          'content' => 'An optional 3-hour climb to Kyanjin Ri (4,773m) offers a 360-degree panorama of the Langtang range.',   'type' => 'info',    'icon_class' => null, 'order' => 3],
                    ],
                ],
            ],

            'Sarangkot Sunrise & Pokhara Valley Trek' => [
                [
                    'name'                        => 'Pokhara Lakeside Start',
                    'description'           => 'Begin at Phewa Lake — Pokhara\'s famous lake reflecting the Annapurna range.',
                    'order'                       => 1,
                    'latitude'                    => 28.2096,
                    'longitude'                   => 83.9560,
                    'radius'                      => 80,
                    'estimated_time_from_previous'=> 10,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'Phewa Lake', 'content' => 'Nepal\'s second largest lake. On clear days the reflection of Machhapuchhre (Fishtail Mountain) is visible from the shore.', 'type' => 'info', 'icon_class' => null, 'order' => 1],
                    ],
                ],
                [
                    'name'                        => 'Sarangkot Viewpoint',
                    'description'           => 'The most celebrated sunrise viewpoint in Nepal at 1,592m — the full Annapurna massif in front of you.',
                    'order'                       => 2,
                    'latitude'                    => 28.2264,
                    'longitude'                   => 83.9558,
                    'radius'                      => 100,
                    'estimated_time_from_previous'=> 120,
                    'image'                       => null,
                    'facts'                       => [
                        ['title' => 'What You Can See',  'content' => 'On a clear day: Dhaulagiri (8,167m), Annapurna I (8,091m), Machhapuchhre (6,993m), and Manaslu (8,163m).', 'type' => 'info',    'icon_class' => null, 'order' => 1],
                        ['title' => 'Best Time',         'content' => 'Arrive before 5:30am October–November, before 6am March–April. The sky colours last only about 30 minutes.',  'type' => 'safety',  'icon_class' => null, 'order' => 2],
                        ['title' => 'Paragliding Hub',   'content' => 'Sarangkot is one of the world\'s top paragliding launch sites. Tandem flights to Phewa Lake take about 30 minutes.', 'type' => 'info', 'icon_class' => null, 'order' => 3],
                    ],
                ],
            ],
        ];

        foreach ($checkpointData as $packageName => $checkpoints) {
            $package = TourPackage::where('name', $packageName)->first();
            if (!$package) continue;

            foreach ($checkpoints as $cpData) {
                $facts = $cpData['facts'];
                unset($cpData['facts']);

                $checkpoint = Checkpoint::updateOrCreate(
                    [
                        'tour_package_id' => $package->id,
                        'order'           => $cpData['order'],
                    ],
                    array_merge($cpData, ['tour_package_id' => $package->id])
                );

                foreach ($facts as $factData) {
                    CheckpointFact::updateOrCreate(
                        [
                            'checkpoint_id' => $checkpoint->id,
                            'title'         => $factData['title'],
                        ],
                        array_merge($factData, ['checkpoint_id' => $checkpoint->id])
                    );
                }
            }
        }

        $this->command->info('     ✓ Checkpoints & facts seeded');
    }

    /*
    |--------------------------------------------------------------------------
    | USERS — one per preference profile for testing
    |--------------------------------------------------------------------------
    */
    private function seedUsers(): void
    {
        $this->command->info('  → Seeding test users...');

        $users = [
            ['name' => 'Aarav Sharma',   'email' => 'aarav@test.com',   'role' => 'user'],
            ['name' => 'Priya Gurung',   'email' => 'priya@test.com',   'role' => 'user'],
            ['name' => 'Bikram Thapa',   'email' => 'bikram@test.com',  'role' => 'user'],
            ['name' => 'Sita Rai',       'email' => 'sita@test.com',    'role' => 'user'],
            ['name' => 'Rohan Tamang',   'email' => 'rohan@test.com',   'role' => 'user'],
            ['name' => 'Admin User',     'email' => 'admin@paaila.com',   'role' => 'admin'],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name'              => $userData['name'],
                    'email'             => $userData['email'],
                    'password'          => Hash::make('password'),
                    'role'              => $userData['role'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('     ✓ ' . count($users) . ' users seeded');
    }

    /*
    |--------------------------------------------------------------------------
    | USER PREFERENCES
    | One profile per user, covering all 4 preference dimensions.
    |--------------------------------------------------------------------------
    */
    private function seedUserPreferences(): void
    {
        $this->command->info('  → Seeding user preferences...');

        $preferences = [
            'aarav@test.com' => [
                'trek_types'        => ['historical', 'cultural'],
                'difficulty'        => 'easy',
                'duration'          => '1-3',
                'budget'            => 'mid',
                'group_size'        => 'couple',
                'preferred_seasons' => ['spring', 'autumn'],
                'preferences_set'   => true,
            ],
            'priya@test.com' => [
                'trek_types'        => ['nature', 'wildlife'],
                'difficulty'        => 'easy',
                'duration'          => '4-7',
                'budget'            => 'budget',
                'group_size'        => 'family',
                'preferred_seasons' => ['winter', 'spring'],
                'preferences_set'   => true,
            ],
            'bikram@test.com' => [
                'trek_types'        => ['adventure'],
                'difficulty'        => 'hard',
                'duration'          => '8-14',
                'budget'            => 'premium',
                'group_size'        => 'solo',
                'preferred_seasons' => ['spring', 'autumn'],
                'preferences_set'   => true,
            ],
            'sita@test.com' => [
                'trek_types'        => ['spiritual', 'cultural'],
                'difficulty'        => 'moderate',
                'duration'          => '4-7',
                'budget'            => 'mid',
                'group_size'        => 'group',
                'preferred_seasons' => ['autumn'],
                'preferences_set'   => true,
            ],
            'rohan@test.com' => [
                'trek_types'        => ['scenic', 'village'],
                'difficulty'        => 'moderate',
                'duration'          => '1-3',
                'budget'            => 'budget',
                'group_size'        => 'solo',
                'preferred_seasons' => ['spring', 'winter'],
                'preferences_set'   => true,
            ],
        ];

        foreach ($preferences as $email => $prefData) {
            $user = User::where('email', $email)->first();
            if (!$user) continue;

            UserPreference::updateOrCreate(
                ['user_id' => $user->id],
                array_merge($prefData, ['user_id' => $user->id])
            );
        }

        $this->command->info('     ✓ User preferences seeded');
    }

    /*
    |--------------------------------------------------------------------------
    | BOOKINGS & RATINGS
    | Creates realistic completed bookings with WORM ratings.
    | Uses writeOnce() to respect the WORM pattern.
    |--------------------------------------------------------------------------
    */
    private function seedBookingsAndRatings(): void
    {
        $this->command->info('  → Seeding bookings & ratings...');

        $scenarios = [
            // Aarav: completed 2 historical treks, rated both highly
            // → "You Liked Earlier" should recommend more historical/cultural
            [
                'email'        => 'aarav@test.com',
                'package_name' => 'Kathmandu Heritage Walking Tour',
                'tour_date'    => Carbon::now()->subDays(30),
                'participants' => 2,
                'status'       => 'completed',
                'started_at'   => Carbon::now()->subDays(30),
                'completed_at' => Carbon::now()->subDays(29),
                'rating'       => 5,
                'review'       => 'Absolutely stunning. The guide was incredibly knowledgeable about every temple and courtyard. Best day in Kathmandu!',
            ],
            [
                'email'        => 'aarav@test.com',
                'package_name' => 'Bhaktapur & Patan Ancient Cities Walk',
                'tour_date'    => Carbon::now()->subDays(60),
                'participants' => 2,
                'status'       => 'completed',
                'started_at'   => Carbon::now()->subDays(60),
                'completed_at' => Carbon::now()->subDays(58),
                'rating'       => 4,
                'review'       => 'Bhaktapur was incredible. Slightly rushed on day 2 but overall a wonderful historical experience.',
            ],

            // Priya: completed nature trek, rated it 5 stars
            // → "You Liked Earlier" should recommend more nature/wildlife
            [
                'email'        => 'priya@test.com',
                'package_name' => 'Chitwan Jungle Safari & Village Trek',
                'tour_date'    => Carbon::now()->subDays(20),
                'participants' => 4,
                'status'       => 'completed',
                'started_at'   => Carbon::now()->subDays(20),
                'completed_at' => Carbon::now()->subDays(18),
                'rating'       => 5,
                'review'       => 'Saw a one-horned rhino up close! The elephant safari at dawn was something our family will never forget.',
            ],
            [
                'email'        => 'priya@test.com',
                'package_name' => 'Shivapuri Forest Day Trek',
                'tour_date'    => Carbon::now()->subDays(45),
                'participants' => 3,
                'status'       => 'completed',
                'started_at'   => Carbon::now()->subDays(45),
                'completed_at' => Carbon::now()->subDays(45),
                'rating'       => 4,
                'review'       => 'Great day hike. Very peaceful and the forest was beautiful. Good for beginners.',
            ],

            // Bikram: completed hard adventure trek, rated 5 stars
            // → "You Liked Earlier" should recommend more adventure/high-altitude
            [
                'email'        => 'bikram@test.com',
                'package_name' => 'Langtang Valley Trek',
                'tour_date'    => Carbon::now()->subDays(15),
                'participants' => 1,
                'status'       => 'completed',
                'started_at'   => Carbon::now()->subDays(15),
                'completed_at' => Carbon::now()->subDays(8),
                'rating'       => 5,
                'review'       => 'One of the best treks I\'ve done in my life. Challenging, emotional (the earthquake memorial), and breathtakingly beautiful.',
            ],

            // Sita: completed spiritual trek, rated 4 stars
            [
                'email'        => 'sita@test.com',
                'package_name' => 'Muktinath Pilgrimage Trek',
                'tour_date'    => Carbon::now()->subDays(25),
                'participants' => 3,
                'status'       => 'completed',
                'started_at'   => Carbon::now()->subDays(25),
                'completed_at' => Carbon::now()->subDays(19),
                'rating'       => 4,
                'review'       => 'A deeply moving pilgrimage. The altitude was challenging but reaching Muktinath felt truly sacred.',
            ],

            // Rohan: completed scenic trek, rated 5 stars
            [
                'email'        => 'rohan@test.com',
                'package_name' => 'Sarangkot Sunrise & Pokhara Valley Trek',
                'tour_date'    => Carbon::now()->subDays(10),
                'participants' => 1,
                'status'       => 'completed',
                'started_at'   => Carbon::now()->subDays(10),
                'completed_at' => Carbon::now()->subDays(10),
                'rating'       => 5,
                'review'       => 'Woke up at 4am and it was 100% worth it. The sunrise over Annapurna was life-changing. Will do it again.',
            ],

            // Active booking — no rating yet (for testing canRate = false)
            [
                'email'        => 'bikram@test.com',
                'package_name' => 'Mardi Himal Base Camp',
                'tour_date'    => Carbon::now()->addDays(5),
                'participants' => 1,
                'status'       => 'confirmed',
                'started_at'   => null,
                'completed_at' => null,
                'rating'       => null,
                'review'       => null,
            ],

            // Pending booking — for testing payment flow UI
            [
                'email'        => 'priya@test.com',
                'package_name' => 'Bardia Tiger Safari Trek',
                'tour_date'    => Carbon::now()->addDays(14),
                'participants' => 2,
                'status'       => 'pending',
                'started_at'   => null,
                'completed_at' => null,
                'rating'       => null,
                'review'       => null,
            ],
        ];

        $bookingCount = 0;
        $ratingCount  = 0;

        foreach ($scenarios as $s) {
            $user    = User::where('email', $s['email'])->first();
            $package = TourPackage::where('name', $s['package_name'])->first();

            if (!$user || !$package) {
                $this->command->warn("     ⚠ Skipping: {$s['email']} / {$s['package_name']}");
                continue;
            }

            // Avoid duplicate bookings
            $existing = TourBooking::where('user_id', $user->id)
                ->where('tour_package_id', $package->id)
                ->first();

            if ($existing) {
                $booking = $existing;
            } else {
                $booking = TourBooking::create([
                    'user_id'          => $user->id,
                    'tour_package_id'  => $package->id,
                    'booking_number'   => 'TRK' . strtoupper(Str::random(8)),
                    'tour_date'        => $s['tour_date'],
                    'participants'     => $s['participants'],
                    'total_amount'     => $package->price * $s['participants'],
                    'payment_method'   => collect(['esewa', 'khalti', 'stripe'])->random(),
                    'status'           => $s['status'],
                    'confirmed_at'     => in_array($s['status'], ['confirmed', 'active', 'completed'])
                                            ? Carbon::now()->subDays(rand(1, 5))
                                            : null,
                    'paid_at'          => in_array($s['status'], ['confirmed', 'active', 'completed'])
                                            ? Carbon::now()->subDays(rand(1, 5))
                                            : null,
                    'started_at'       => $s['started_at'],
                    'completed_at'     => $s['completed_at'],
                ]);

                $bookingCount++;

                // Create tracking pin for confirmed/active/completed bookings
                if (in_array($s['status'], ['confirmed', 'active', 'completed'])) {
                    $pin = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);

                    // Ensure unique PIN
                    while (\App\Models\TrackingPin::where('pin', $pin)->exists()) {
                        $pin = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                    }

                    \App\Models\TrackingPin::create([
                        'tour_booking_id' => $booking->id,
                        'pin'             => $pin,
                        'is_active'       => true,
                        'expires_at'      => Carbon::now()->addHours(72),
                        'access_count'    => rand(0, 5),
                        'last_accessed_at'=> $s['status'] === 'completed' ? Carbon::now()->subDays(1) : null,
                    ]);

                    // Seed checkpoint progress for completed bookings
                    if ($s['status'] === 'completed') {
                        $checkpoints = $package->checkpoints()->orderBy('order')->get();
                        foreach ($checkpoints as $cp) {
                            CheckpointProgress::updateOrCreate(
                                [
                                    'tour_booking_id' => $booking->id,
                                    'checkpoint_id'   => $cp->id,
                                ],
                                [
                                    'reached_at'               => Carbon::now()->subDays(rand(1, 3)),
                                    'facts_viewed'             => true,
                                    'distance_from_checkpoint' => 0,
                                ]
                            );
                        }
                    }
                }
            }

            // Write-once rating for completed bookings
            if ($s['rating'] && $booking->status === 'completed') {
                $alreadyRated = TrekRating::hasRated($user->id, $package->id);

                if (!$alreadyRated) {
                    $result = TrekRating::writeOnce(
                        packageId: $package->id,
                        userId:    $user->id,
                        bookingId: $booking->id,
                        stars:     $s['rating'],
                        review:    $s['review'],
                    );

                    if ($result['success']) {
                        $ratingCount++;
                    } else {
                        $this->command->warn('     ⚠ Rating failed: ' . $result['reason']);
                    }
                }
            }
        }

        $this->command->info("     ✓ {$bookingCount} bookings created, {$ratingCount} ratings written");
    }
}