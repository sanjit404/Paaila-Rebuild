<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\TourPackage;
use App\Models\Checkpoint;
use App\Models\CheckpointFact;

class PgSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin User ───────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@paaila.com'],
            [
                'name'     => 'Paaila Admin',
                'email'    => 'admin@paaila.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // ─── Trek Packages ────────────────────────────────────────────────────
        foreach ($this->packages() as $pkgData) {
            $checkpoints = $pkgData['checkpoints'];
            unset($pkgData['checkpoints']);

            $package = TourPackage::firstOrCreate(
                ['name' => $pkgData['name']],  // match on name
                $pkgData                        // only set these on first create
            );

            // Skip checkpoints if package already existed
            if (!$package->wasRecentlyCreated) {
                continue;
            }

            foreach ($checkpoints as $order => $cpData) {
                $facts = $cpData['facts'];
                unset($cpData['facts']);

                $cpData['tour_package_id'] = $package->id;
                $cpData['order']           = $order + 1;

                $checkpoint = Checkpoint::create($cpData);

                foreach ($facts as $factOrder => $factData) {
                    CheckpointFact::create(array_merge($factData, [
                        'checkpoint_id' => $checkpoint->id,
                        'order'         => $factOrder + 1,
                    ]));
                }
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DATA
    // ─────────────────────────────────────────────────────────────────────────

    private function packages(): array
    {
        return [

            // ══════════════════════════════════════════════════════════════════
            // 1. Everest Base Camp Trek
            // ══════════════════════════════════════════════════════════════════
            [
                'name'                => 'Everest Base Camp Trek',
                'description'         => 'The iconic journey to the foot of the world\'s highest mountain. Walk in the footsteps of legends through Sherpa villages, ancient monasteries, and breathtaking Himalayan scenery. Starting from the legendary Tenzing-Hillary Airport in Lukla, this trek takes you through the Khumbu region, culminating at Everest Base Camp at 5,364 m. Experience the unique Sherpa culture, acclimatise at Namche Bazaar, and witness the stunning Khumbu Icefall up close.',
                'trek_type'           => 'adventure',
                'tags'                => json_encode(['himalaya', 'everest', 'sherpa', 'high-altitude', 'iconic']),
                'season'              => json_encode(['spring', 'autumn']),
                'region'              => 'Khumbu, Solukhumbu',
                'price'               => 1800.00,
                'duration_days'       => 14,
                'difficulty_level'    => 'hard',
                'max_participants'    => 12,
                'image'               => 'https://images.unsplash.com/photo-1516912481808-3406841bd33c?w=1200',
                'start_location_name' => 'Lukla (Tenzing-Hillary Airport)',
                'start_lat'           => 27.6869,
                'start_lng'           => 86.7314,
                'end_location_name'   => 'Everest Base Camp',
                'end_lat'             => 28.0026,
                'end_lng'             => 86.8528,
                'is_active'           => true,
                'views_count'         => 1240,
                'bookings_count'      => 87,
                'rating_avg'          => 4.92,
                'rating_count'        => 74,
                'checkpoints'         => [
                    [
                        'name'                         => 'Lukla — Tenzing-Hillary Airport',
                        'description'                  => 'Your trek begins at one of the world\'s most thrilling airports, perched at 2,860 m with a short sloping runway carved into the mountainside. The small bazaar town of Lukla is bustling with trekkers, tea houses, and yak caravans.',
                        'image'                        => 'https://images.unsplash.com/photo-1605640840605-14ac1855827b?w=800',
                        'latitude'                     => 27.6869,
                        'longitude'                    => 86.7314,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 0,
                        'facts' => [
                            ['title' => 'Altitude',           'content' => 'Lukla sits at 2,860 m (9,383 ft). Even at this starting point, you may begin to feel the thin air if you have flown in from Kathmandu at 1,400 m. Take it easy on day one.',                                                                                                                  'type' => 'info',       'icon_class' => 'fas fa-mountain'],
                            ['title' => 'Named After a Legend','content' => 'The airport is officially named Tenzing-Hillary Airport in honour of Tenzing Norgay Sherpa and Sir Edmund Hillary, the first two climbers to summit Everest on 29 May 1953.',                                                                                                                    'type' => 'historical', 'icon_class' => 'fas fa-landmark'],
                            ['title' => 'Safety Tip',          'content' => 'Flights to Lukla are weather-dependent and frequently delayed or cancelled. Always build buffer days into your itinerary and never book a tight onward flight from Kathmandu.',                                                                                                                      'type' => 'safety',     'icon_class' => 'fas fa-exclamation-triangle'],
                        ],
                    ],
                    [
                        'name'                         => 'Namche Bazaar',
                        'description'                  => 'The gateway to the high Himalayas and the capital of the Khumbu region at 3,440 m. A horseshoe-shaped town with colourful lodges, a famous Saturday market, and the first views of Everest for trekkers heading uphill.',
                        'image'                        => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=800',
                        'latitude'                     => 27.8069,
                        'longitude'                    => 86.7139,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 390,
                        'facts' => [
                            ['title' => 'Acclimatisation Day', 'content' => 'Spend two nights in Namche before proceeding higher. On your acclimatisation day, hike up to the Everest View Hotel (3,880 m) for your first panoramic view of Everest, Lhotse, and Ama Dablam.',                                                                                             'type' => 'tip',        'icon_class' => 'fas fa-lightbulb'],
                            ['title' => 'Sherpa Capital',      'content' => 'Namche is the commercial and cultural hub of the Sherpa people. The Sagarmatha National Park headquarters and the Sherpa Culture Museum are both here — worth an hour of your acclimatisation day.',                                                                                              'type' => 'cultural',   'icon_class' => 'fas fa-users'],
                            ['title' => 'Saturday Market',     'content' => 'Namche\'s famous weekly market draws traders from as far as Tibet. You can find everything from yak cheese and Tibetan salt to North Face gear (genuine and otherwise).',                                                                                                                          'type' => 'cultural',   'icon_class' => 'fas fa-store'],
                            ['title' => 'Altitude Warning',    'content' => 'Above 3,000 m, ascend no more than 300–500 m of sleeping altitude per day. Namche is the standard acclimatisation stop because ignoring this rule causes Acute Mountain Sickness (AMS).',                                                                                                         'type' => 'safety',     'icon_class' => 'fas fa-heartbeat'],
                        ],
                    ],
                    [
                        'name'                         => 'Tengboche Monastery',
                        'description'                  => 'The most famous monastery in the Khumbu, sitting at 3,867 m with a jaw-dropping backdrop of Ama Dablam, Everest, and Nuptse. Tengboche is the spiritual heart of the Sherpa world and the site of the Mani Rimdu festival.',
                        'image'                        => 'https://images.unsplash.com/photo-1570461226513-e08b58a52c53?w=800',
                        'latitude'                     => 27.8361,
                        'longitude'                    => 86.7642,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 300,
                        'facts' => [
                            ['title' => 'Founded in 1916',      'content' => 'Tengboche Monastery (Thyangboche) was founded by Lama Gulu in 1916. It was destroyed by an earthquake in 1934, rebuilt, burned down in 1989, and rebuilt again — the current structure dates from 1993.',                                                                                       'type' => 'historical', 'icon_class' => 'fas fa-history'],
                            ['title' => 'Mani Rimdu Festival',  'content' => 'The three-day Mani Rimdu festival, held in October/November at full moon, features masked dances performed by monks. It is one of the most spectacular cultural events in the Himalayas and a rare privilege to witness.',                                                                        'type' => 'cultural',   'icon_class' => 'fas fa-theater-masks'],
                            ['title' => 'Respectful Behaviour', 'content' => 'Remove your shoes before entering the monastery. Walk clockwise around all mani walls and stupas (chortens). Do not photograph monks or religious ceremonies without explicit permission.',                                                                                                        'type' => 'tip',        'icon_class' => 'fas fa-hands'],
                        ],
                    ],
                    [
                        'name'                         => 'Gorak Shep & Kala Patthar',
                        'description'                  => 'The last settlement before Base Camp at 5,164 m, Gorak Shep is a cluster of lodges on an ancient glacial lake bed. Kala Patthar (5,545 m) above it offers the finest close-up view of Everest\'s south face of any trekking summit.',
                        'image'                        => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800',
                        'latitude'                     => 27.9799,
                        'longitude'                    => 86.8317,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 480,
                        'facts' => [
                            ['title' => 'Best Sunrise on Earth', 'content' => 'Summiting Kala Patthar at sunrise (around 05:30) gives you an unobstructed golden-hour view of Everest\'s summit pyramid, the West Ridge, and the Khumbu Icefall. Most trekkers rank it the highlight of the entire journey.',                                                               'type' => 'tip',     'icon_class' => 'fas fa-sun'],
                            ['title' => 'Extreme Cold',          'content' => 'Night temperatures at Gorak Shep regularly drop to −15 °C to −20 °C even in peak season. Pack a sleeping bag rated to at least −20 °C and wear all your layers to bed.',                                                                                                                       'type' => 'safety',  'icon_class' => 'fas fa-thermometer-empty'],
                            ['title' => 'Glacial Lake Bed',      'content' => 'Gorak Shep sits on the dried-out bed of a proglacial lake that existed when the Khumbu Glacier extended this far. The flat, sandy terrain among towering moraines is geologically unique in the region.',                                                                                        'type' => 'natural', 'icon_class' => 'fas fa-water'],
                        ],
                    ],
                    [
                        'name'                         => 'Everest Base Camp (5,364 m)',
                        'description'                  => 'The ultimate destination — the legendary Everest Base Camp at 5,364 m on the lateral moraine of the Khumbu Glacier. During climbing season (April–May) you will see the colourful tent city of Everest expeditions. The Khumbu Icefall looms directly above.',
                        'image'                        => 'https://images.unsplash.com/photo-1606132399145-ced8d6a7c19e?w=800',
                        'latitude'                     => 28.0026,
                        'longitude'                    => 86.8528,
                        'radius'                       => 120,
                        'estimated_time_from_previous' => 120,
                        'facts' => [
                            ['title' => 'Not the Summit View',  'content' => 'Ironically, you cannot see the summit of Everest from Base Camp — it is hidden behind Nuptse. The best summit view is from Kala Patthar. Base Camp is instead about being at the doorstep of the world\'s greatest climb.',                                                                      'type' => 'info',       'icon_class' => 'fas fa-info-circle'],
                            ['title' => 'The Khumbu Icefall',   'content' => 'The Khumbu Icefall, directly above Base Camp, is considered one of the most dangerous sections of the Everest ascent. Seracs and crevasses shift daily. Only permitted climbers with the Icefall Doctors\' fixed ropes may enter.',                                                               'type' => 'natural',    'icon_class' => 'fas fa-icicles'],
                            ['title' => 'First Expedition',     'content' => 'The first Everest Base Camp was established by the 1953 British expedition led by John Hunt. Since then, thousands of climbers and hundreds of thousands of trekkers have stood on this very moraine.',                                                                                             'type' => 'historical', 'icon_class' => 'fas fa-flag'],
                            ['title' => 'Leave No Trace',       'content' => 'Base Camp has a serious litter problem from decades of expeditions. Carry all your waste out. Do not touch or move any equipment left by climbing teams — it is their safety gear.',                                                                                                               'type' => 'safety',     'icon_class' => 'fas fa-recycle'],
                        ],
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════════════════════
            // 2. Annapurna Circuit Trek
            // ══════════════════════════════════════════════════════════════════
            [
                'name'                => 'Annapurna Circuit Trek',
                'description'         => 'One of the world\'s greatest trekking circuits, circumnavigating the entire Annapurna massif through dramatically diverse landscapes — from subtropical river valleys and terraced rice fields to high-altitude desert plateau and glaciated peaks. The highlight is crossing the Thorong La Pass at 5,416 m, one of the highest trekking passes in the world. The circuit passes through dozens of ethnic communities: Gurung, Magar, Thakali, and Mustangi.',
                'trek_type'           => 'scenic',
                'tags'                => json_encode(['annapurna', 'circuit', 'pass', 'diverse', 'cultural']),
                'season'              => json_encode(['spring', 'autumn']),
                'region'              => 'Annapurna, Gandaki',
                'price'               => 1200.00,
                'duration_days'       => 18,
                'difficulty_level'    => 'hard',
                'max_participants'    => 16,
                'image'               => 'https://images.unsplash.com/photo-1585409677983-0f6c41ca9c3b?w=1200',
                'start_location_name' => 'Besisahar',
                'start_lat'           => 28.2389,
                'start_lng'           => 84.3817,
                'end_location_name'   => 'Pokhara',
                'end_lat'             => 28.2096,
                'end_lng'             => 83.9856,
                'is_active'           => true,
                'views_count'         => 980,
                'bookings_count'      => 63,
                'rating_avg'          => 4.85,
                'rating_count'        => 58,
                'checkpoints'         => [
                    [
                        'name'                         => 'Chame (2,710 m)',
                        'description'                  => 'The district headquarters of Manang district, Chame is a large, well-supplied village with excellent bakeries and apple orchards. It marks the point where the valley narrows dramatically and the real Himalayan scenery begins.',
                        'image'                        => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800',
                        'latitude'                     => 28.5472,
                        'longitude'                    => 84.2289,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 420,
                        'facts' => [
                            ['title' => 'Apple Country',          'content' => 'The Manang and Mustang regions are Nepal\'s apple-growing heartland. Local apple brandy (raksi) and apple pie are regional specialities you will find in every tea house from Chame onward. Try the fresh-pressed juice in season (September–November).', 'type' => 'cultural', 'icon_class' => 'fas fa-apple-alt'],
                            ['title' => 'Hot Springs Nearby',     'content' => 'A short detour from Chame leads to natural hot springs — a blissful soak for tired legs at the end of a long walking day. Ask your guide or tea house host for directions.',                                                                           'type' => 'tip',      'icon_class' => 'fas fa-hot-tub'],
                            ['title' => 'Paungda Danda Rock Face', 'content' => 'Just above Chame, the trail passes beneath a spectacular curved rock face rising nearly 1,500 m straight from the valley floor — one of the most dramatic geological formations on the entire circuit.',                                                 'type' => 'natural',  'icon_class' => 'fas fa-mountain'],
                        ],
                    ],
                    [
                        'name'                         => 'Manang (3,519 m)',
                        'description'                  => 'A large Tibetan-influenced village with flat-roofed stone houses, prayer flags, and sweeping views of the Annapurna III, Gangapurna, and Tilicho Peak. The Himalayan Rescue Association (HRA) runs a free daily altitude lecture here — mandatory viewing.',
                        'image'                        => 'https://images.unsplash.com/photo-1606041011872-596597976b25?w=800',
                        'latitude'                     => 28.6694,
                        'longitude'                    => 84.0219,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 360,
                        'facts' => [
                            ['title' => 'HRA Altitude Lecture',  'content' => 'The Himalayan Rescue Association (HRA) post in Manang gives a free lecture every afternoon at 3 PM on Acute Mountain Sickness, High Altitude Pulmonary Oedema (HAPO), and High Altitude Cerebral Oedema (HACO). Attendance is strongly recommended before crossing Thorong La.', 'type' => 'safety',  'icon_class' => 'fas fa-stethoscope'],
                            ['title' => 'Gangapurna Lake',       'content' => 'A turquoise glacial lake just 20 minutes above the village, fed by the Gangapurna Glacier. The reflection of Gangapurna peak (7,454 m) in the still morning water is one of the circuit\'s most photographed scenes.',                                                              'type' => 'natural', 'icon_class' => 'fas fa-water'],
                            ['title' => 'Acclimatisation Hike',  'content' => 'Hike up to the Ice Lake (Kicho Tal) at 4,600 m on your acclimatisation day — a challenging 5-hour round trip that rewards you with astonishing 360° views and dramatically improves your body\'s readiness for Thorong La.',                                                          'type' => 'tip',     'icon_class' => 'fas fa-hiking'],
                        ],
                    ],
                    [
                        'name'                         => 'Thorong La Pass (5,416 m)',
                        'description'                  => 'The highest point of the Annapurna Circuit and the crux of the entire journey. A gruelling pre-dawn ascent of nearly 1,000 m from Thorong Phedi rewards you with one of the most exhilarating high-altitude crossings in trekking. The descent to Muktinath on the other side is a knee-testing 1,600 m drop.',
                        'image'                        => 'https://images.unsplash.com/photo-1601439678777-b870ad272c97?w=800',
                        'latitude'                     => 28.7947,
                        'longitude'                    => 83.9308,
                        'radius'                       => 120,
                        'estimated_time_from_previous' => 480,
                        'facts' => [
                            ['title' => 'Start Before Dawn',                    'content' => 'Leave Thorong High Camp or Thorong Phedi no later than 04:00. Afternoon winds on the pass are dangerously strong and can make the crossing impossible. The vast majority of turnarounds and accidents happen to trekkers who started too late.', 'type' => 'safety',   'icon_class' => 'fas fa-wind'],
                            ['title' => 'Highest Trekking Pass in the World',   'content' => 'At 5,416 m, Thorong La is among the highest trekking passes on earth. The air contains roughly half the oxygen of sea level. Go slowly, breathe deeply, and never separate from your group.',                                               'type' => 'info',     'icon_class' => 'fas fa-flag'],
                            ['title' => 'Prayer Flags & Cairns',                'content' => 'The pass is festooned with colourful Tibetan prayer flags that are believed to spread prayers and blessings on the wind. Trekkers traditionally add a stone to the summit cairn — a small ritual that connects you to every traveller who has crossed before you.', 'type' => 'cultural', 'icon_class' => 'fas fa-pray'],
                        ],
                    ],
                    [
                        'name'                         => 'Muktinath Temple (3,760 m)',
                        'description'                  => 'One of the holiest sites in both Hinduism and Buddhism, Muktinath is a sacred temple complex with 108 waterspout bull heads, a natural flame fed by underground natural gas, and a Buddhist gompa. Pilgrims come year-round from across South Asia.',
                        'image'                        => 'https://images.unsplash.com/photo-1582650949431-6e328e5891d2?w=800',
                        'latitude'                     => 28.8175,
                        'longitude'                    => 83.8722,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 120,
                        'facts' => [
                            ['title' => 'Sacred to Two Religions', 'content' => 'Muktinath (Liberation Place) is one of the 108 Divya Desams — sacred Vishnu temples — for Hindus, and simultaneously an important Buddhist site associated with Guru Rinpoche (Padmasambhava). Both communities worship here peacefully side by side.', 'type' => 'cultural',   'icon_class' => 'fas fa-om'],
                            ['title' => 'The Eternal Flame',        'content' => 'Inside the Jwala Mai temple burns a small natural flame fuelled by underground natural gas seeping through water — a phenomenon that has burned for centuries and is considered miraculous by pilgrims of both faiths.',                                    'type' => 'natural',    'icon_class' => 'fas fa-fire'],
                            ['title' => '108 Bull Waterspouts',     'content' => 'The temple compound features 108 bronze bull-head waterspouts arranged in a semi-circle. Devout pilgrims bathe under all 108 spouts, which flow with ice-cold Himalayan spring water, believing it cleanses sin and grants liberation (mukti).',          'type' => 'historical', 'icon_class' => 'fas fa-tint'],
                        ],
                    ],
                    [
                        'name'                         => 'Poon Hill Viewpoint (3,210 m)',
                        'description'                  => 'The most famous sunrise viewpoint in Nepal, offering a panoramic vista of over a dozen 7,000+ m and 8,000+ m peaks including Dhaulagiri, Annapurna I, Annapurna South, Hiunchuli, Machhapuchhre (Fishtail), and Annapurna III — all glowing gold at dawn.',
                        'image'                        => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800',
                        'latitude'                     => 28.3985,
                        'longitude'                    => 83.6922,
                        'radius'                       => 80,
                        'estimated_time_from_previous' => 240,
                        'facts' => [
                            ['title' => 'Pre-Dawn Ascent',     'content' => 'Wake at 04:30 and hike the 45-minute trail from Ghorepani village with a headtorch. Arrive before sunrise to claim a good spot. Hundreds of trekkers converge here each morning in peak season — the camaraderie at the summit is electric.',           'type' => 'tip',     'icon_class' => 'fas fa-sun'],
                            ['title' => 'Panorama of Giants',  'content' => 'From Poon Hill you can see eight peaks above 7,000 m, including two of the world\'s fourteen 8,000ers: Dhaulagiri (8,167 m) and Annapurna I (8,091 m). On a clear day the view stretches over 200 km of the Himalayan chain.',                          'type' => 'natural', 'icon_class' => 'fas fa-binoculars'],
                            ['title' => 'Rhododendron Forests', 'content' => 'The trail to Poon Hill passes through Nepal\'s largest rhododendron forests. In March and April, these hillsides explode in red, pink, and white blooms — a spectacle as memorable as the mountain views themselves.',                                  'type' => 'natural', 'icon_class' => 'fas fa-leaf'],
                        ],
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════════════════════
            // 3. Langtang Valley Trek
            // ══════════════════════════════════════════════════════════════════
            [
                'name'                => 'Langtang Valley Trek',
                'description'         => 'Nepal\'s closest major trekking destination to Kathmandu, the Langtang Valley offers a deeply moving combination of dramatic mountain scenery, rich Tamang culture, and powerful post-earthquake resilience. The valley was devastated by the April 2015 earthquake and subsequent landslide — trekking here is also a meaningful act of support for the community that has worked so hard to rebuild. The valley culminates at the sacred Gosaikunda Lakes.',
                'trek_type'           => 'nature',
                'tags'                => json_encode(['langtang', 'tamang', 'valley', 'close-to-kathmandu', 'cultural']),
                'season'              => json_encode(['spring', 'autumn', 'winter']),
                'region'              => 'Langtang, Rasuwa',
                'price'               => 650.00,
                'duration_days'       => 7,
                'difficulty_level'    => 'moderate',
                'max_participants'    => 16,
                'image'               => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=1200',
                'start_location_name' => 'Syabrubesi',
                'start_lat'           => 28.1583,
                'start_lng'           => 85.3481,
                'end_location_name'   => 'Kyanjin Gompa',
                'end_lat'             => 28.2124,
                'end_lng'             => 85.5648,
                'is_active'           => true,
                'views_count'         => 760,
                'bookings_count'      => 45,
                'rating_avg'          => 4.78,
                'rating_count'        => 39,
                'checkpoints'         => [
                    [
                        'name'                         => 'Syabrubesi (1,550 m)',
                        'description'                  => 'The trailhead village at the confluence of the Langtang and Bhote Koshi rivers. A compact town with guesthouses, small shops, and the energy of trekkers gathering before heading into the valley.',
                        'image'                        => 'https://images.unsplash.com/photo-1605640840605-14ac1855827b?w=800',
                        'latitude'                     => 28.1583,
                        'longitude'                    => 85.3481,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 0,
                        'facts' => [
                            ['title' => 'Gateway to Langtang', 'content' => 'Syabrubesi is 117 km north of Kathmandu and can be reached by a 6–7 hour bus ride via Trishuli Bazaar and Dhunche. A private jeep takes about 5 hours. The road passes through spectacular gorge scenery.', 'type' => 'info',     'icon_class' => 'fas fa-road'],
                            ['title' => 'Tamang People',        'content' => 'The Langtang Valley is home to the Tamang people, one of Nepal\'s major indigenous groups with Tibetan-Buddhist roots. They are known for their warm hospitality, distinctive jewellery, and traditional round stone houses.',    'type' => 'cultural', 'icon_class' => 'fas fa-users'],
                        ],
                    ],
                    [
                        'name'                         => 'Lama Hotel (2,470 m)',
                        'description'                  => 'A cluster of tea houses in a dense forest of oak, rhododendron, and maple. The forest is rich with wildlife — red pandas, Himalayan black bears, and langur monkeys have all been spotted along this section of trail.',
                        'image'                        => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800',
                        'latitude'                     => 28.1711,
                        'longitude'                    => 85.4214,
                        'radius'                       => 80,
                        'estimated_time_from_previous' => 270,
                        'facts' => [
                            ['title' => 'Red Panda Habitat',      'content' => 'The temperate forests between Syabrubesi and Lama Hotel are one of the best places in Nepal to spot the elusive red panda (Ailurus fulgens). Look for russet fur in the trees early morning and at dusk.',                                                                'type' => 'natural', 'icon_class' => 'fas fa-paw'],
                            ['title' => 'Langtang National Park', 'content' => 'You entered Langtang National Park just above Syabrubesi. Established in 1976, it was Nepal\'s first Himalayan national park and covers 1,710 km². Your TIMS card and park entry permit will be checked here.',                                                         'type' => 'info',    'icon_class' => 'fas fa-tree'],
                            ['title' => 'Leech Season Warning',   'content' => 'During monsoon (June–August), the trail between Syabrubesi and Lama Hotel is heavily infested with leeches. Tuck trousers into socks, apply salt or insect repellent to boots, and check regularly. This is a major reason autumn and spring are preferred seasons.', 'type' => 'safety',  'icon_class' => 'fas fa-bug'],
                        ],
                    ],
                    [
                        'name'                         => 'Langtang Village (3,430 m)',
                        'description'                  => 'The rebuilt heart of the valley. The original Langtang Village was completely obliterated on 25 April 2015 when the earthquake triggered a massive ice and rock avalanche from Langtang Lirung, killing over 350 people. The new village, built slightly to the side, is a testament to the community\'s extraordinary resilience.',
                        'image'                        => 'https://images.unsplash.com/photo-1570461226513-e08b58a52c53?w=800',
                        'latitude'                     => 28.2008,
                        'longitude'                    => 85.5167,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 360,
                        'facts' => [
                            ['title' => '2015 Earthquake Memorial', 'content' => 'A simple memorial to the 350+ victims of the 2015 disaster stands in the village. Take a quiet moment here. Many trekking guides working in Langtang today lost family members on that day. Treat the subject with sensitivity.', 'type' => 'historical', 'icon_class' => 'fas fa-landmark'],
                            ['title' => 'Cheese Factory',            'content' => 'The valley is famous for its yak-milk cheese, produced at the Swiss-funded cheese factory. The hard, nutty Langtang cheese is sold at tea houses throughout the valley and makes a wonderful gift to carry back to Kathmandu.',     'type' => 'cultural',   'icon_class' => 'fas fa-cheese'],
                            ['title' => 'Langtang Lirung',           'content' => 'The peak dominating the north side of the valley — Langtang Lirung (7,227 m) — was first summited in 1978 by a Japanese expedition. Its south face, which towers above the valley, is one of the largest glaciated walls in Nepal.', 'type' => 'natural',    'icon_class' => 'fas fa-mountain'],
                        ],
                    ],
                    [
                        'name'                         => 'Kyanjin Gompa (3,870 m)',
                        'description'                  => 'The end of the valley road and the cultural and spiritual heart of Langtang. A small but significant monastery, a government cheese factory, dramatic yak pastures, and towering glacier-hung peaks on three sides. Many trekkers spend two nights here to acclimatise and day-hike higher.',
                        'image'                        => 'https://images.unsplash.com/photo-1606132399145-ced8d6a7c19e?w=800',
                        'latitude'                     => 28.2124,
                        'longitude'                    => 85.5648,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 120,
                        'facts' => [
                            ['title' => 'Tserko Ri Day Hike', 'content' => 'From Kyanjin, hike up to Tserko Ri (4,984 m) — a stunning 5-hour round trip with a 360° panorama of Langtang Lirung, Yala Peak, Dorje Lakpa, and on clear days, all the way to Shishapangma in Tibet. The single best viewpoint in the valley.',                        'type' => 'tip',     'icon_class' => 'fas fa-binoculars'],
                            ['title' => 'Kyanjin Gompa',      'content' => 'The small monastery at Kyanjin dates from the 17th century and is dedicated to Guru Rinpoche. Morning and evening puja (prayer) ceremonies are open to respectful visitors. The sound of the monks\' horns echoing off the glaciers is unforgettable.',                     'type' => 'cultural', 'icon_class' => 'fas fa-place-of-worship'],
                            ['title' => 'Yak Grazing',         'content' => 'The pastures (kharka) around Kyanjin are summer grazing grounds for yaks, the indispensable high-altitude animal of the Himalayas. Give yaks a wide berth on narrow trails — yield uphill-side to them, as they may push you off a ledge if startled.',                  'type' => 'safety',  'icon_class' => 'fas fa-exclamation-circle'],
                            ['title' => 'Altitude & Weather',  'content' => 'At nearly 4,000 m, night temperatures at Kyanjin drop well below freezing even in October. The valley acts as a wind tunnel, producing powerful gusts in the afternoon. Schedule long day hikes for morning completion.',                                                    'type' => 'safety',  'icon_class' => 'fas fa-wind'],
                        ],
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════════════════════
            // 4. Upper Mustang Trek
            // ══════════════════════════════════════════════════════════════════
            [
                'name'                => 'Upper Mustang Trek — Forbidden Kingdom',
                'description'         => 'A journey into Nepal\'s last surviving Tibetan kingdom, hidden behind the Annapurna and Dhaulagiri massifs. Upper Mustang (the former Kingdom of Lo) was closed to foreigners until 1992 and retains an astonishing medieval Tibetan character: whitewashed cave dwellings carved into ochre cliffs, 600-year-old walled cities, sky-burial sites, and winding canyon trails through a surreal arid landscape that feels more like Tibet than Nepal. A restricted-area permit (USD 500) is required.',
                'trek_type'           => 'cultural',
                'tags'                => json_encode(['mustang', 'tibetan', 'restricted', 'desert', 'ancient', 'hidden-kingdom']),
                'season'              => json_encode(['spring', 'summer', 'autumn']),
                'region'              => 'Mustang, Gandaki',
                'price'               => 2200.00,
                'duration_days'       => 15,
                'difficulty_level'    => 'moderate',
                'max_participants'    => 10,
                'image'               => 'https://images.unsplash.com/photo-1601439678777-b870ad272c97?w=1200',
                'start_location_name' => 'Kagbeni',
                'start_lat'           => 28.8369,
                'start_lng'           => 83.7794,
                'end_location_name'   => 'Lo Manthang',
                'end_lat'             => 29.1836,
                'end_lng'             => 83.9656,
                'is_active'           => true,
                'views_count'         => 640,
                'bookings_count'      => 28,
                'rating_avg'          => 4.96,
                'rating_count'        => 25,
                'checkpoints'         => [
                    [
                        'name'                         => 'Kagbeni (2,810 m) — Gateway to Upper Mustang',
                        'description'                  => 'The boundary village where the Upper Mustang restricted area begins. A medieval-looking cluster of flat-roofed mud-brick houses at the confluence of the Kali Gandaki and Jhong rivers, with a striking red-walled monastery dominating the skyline.',
                        'image'                        => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800',
                        'latitude'                     => 28.8369,
                        'longitude'                    => 83.7794,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 0,
                        'facts' => [
                            ['title' => 'Restricted Area Permit', 'content' => 'Beyond Kagbeni checkpoint you must have the Upper Mustang Restricted Area Permit (USD 500 for 10 days, USD 50/day thereafter) plus a regular TIMS card and Annapurna Conservation Area Permit. You must trek with a licensed guide — independent trekking is not permitted.', 'type' => 'info',    'icon_class' => 'fas fa-id-card'],
                            ['title' => 'Kali Gandaki Gorge',     'content' => 'Kagbeni sits at the top of the Kali Gandaki Gorge — the deepest gorge on earth, flanked by Dhaulagiri (8,167 m) and Annapurna I (8,091 m), separated by just 34 km. The gorge is also an ancient trade route between Nepal and Tibet.',                                      'type' => 'natural', 'icon_class' => 'fas fa-water'],
                            ['title' => 'Shaligram Fossils',       'content' => 'The dry riverbed of the Kali Gandaki around Kagbeni is world-famous for shaligram ammonite fossils — spiral-shaped marine fossils from the Jurassic period (180 million years ago) when this entire region was a seabed. Sacred to Hindus as manifestations of Vishnu.',          'type' => 'natural', 'icon_class' => 'fas fa-circle-notch'],
                        ],
                    ],
                    [
                        'name'                         => 'Chele (3,050 m)',
                        'description'                  => 'The first village beyond the checkpoint, perched on a hillside with views down the arid Mustang canyon. The landscape changes dramatically here — from the green Kali Gandaki valley to the stark, wind-eroded badlands of Upper Mustang.',
                        'image'                        => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                        'latitude'                     => 28.9019,
                        'longitude'                    => 83.8172,
                        'radius'                       => 80,
                        'estimated_time_from_previous' => 270,
                        'facts' => [
                            ['title' => 'The Wind of Mustang', 'content' => 'Mustang lies in the rain shadow of the Himalayas and is renowned for powerful afternoon winds that funnel up the Kali Gandaki valley from the south. These winds can reach 80 km/h. Walk in the mornings and rest in the afternoons.',                                                                                                                                   'type' => 'safety',     'icon_class' => 'fas fa-wind'],
                            ['title' => 'Cliff Dwellings',     'content' => 'The eroded red and ochre cliffs above Chele are honeycombed with hundreds of ancient cave dwellings carved into the rock — some 2,000–3,000 years old, used by early inhabitants of the Mustang plateau. Recent archaeological excavations have found remarkable manuscripts, wall paintings, and even mummified human remains.', 'type' => 'historical', 'icon_class' => 'fas fa-home'],
                        ],
                    ],
                    [
                        'name'                         => 'Tsarang (3,560 m)',
                        'description'                  => 'The second-largest settlement in Upper Mustang and former seat of a minor kingdom, Tsarang (Charang) has a magnificent dzong (fortress) and a monastery containing rare 500-year-old thangka paintings and Buddhist manuscripts. The village\'s Luri Gompa cave-monastery nearby is even older.',
                        'image'                        => 'https://images.unsplash.com/photo-1582650949431-6e328e5891d2?w=800',
                        'latitude'                     => 29.1403,
                        'longitude'                    => 83.9642,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 300,
                        'facts' => [
                            ['title' => 'Charang Dzong',            'content' => 'The four-storey whitewashed fortress of Charang Dzong dominates the village skyline. It once served as the residence of a local lord who paid tribute to the King of Lo. The dzong and its monastery contain some of the oldest and finest religious art in the entire Mustang region.',        'type' => 'historical', 'icon_class' => 'fas fa-chess-rook'],
                            ['title' => 'Luri Gompa Cave Monastery', 'content' => 'A 30-minute walk from Tsarang, Luri Gompa is a cave monastery dating to the 14th century, with extraordinary mandala paintings in the main chapel. It is one of the least-visited and most beautifully preserved sacred sites in the Himalayas.',                                          'type' => 'cultural',   'icon_class' => 'fas fa-place-of-worship'],
                            ['title' => 'Apple Orchards',            'content' => 'Tsarang\'s surprisingly lush apple orchards are a startling contrast to the surrounding desert landscape. In October the trees are heavy with fruit and locals are pressing juice and making brandy. A cup of fresh-pressed apple juice here is a trekking memory you will not forget.',     'type' => 'natural',    'icon_class' => 'fas fa-apple-alt'],
                        ],
                    ],
                    [
                        'name'                         => 'Lo Manthang (3,840 m) — The Forbidden City',
                        'description'                  => 'The walled capital of the ancient Kingdom of Lo — one of the most extraordinary and remote towns in Asia. A compact medieval city of whitewashed houses enclosed by a high wall, with four ancient temples, the royal palace, and an atmosphere unchanged for centuries. Home to roughly 200 permanent residents and the seat of the Mustang royal family.',
                        'image'                        => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800',
                        'latitude'                     => 29.1836,
                        'longitude'                    => 83.9656,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 210,
                        'facts' => [
                            ['title' => 'Kingdom of Lo',   'content' => 'Lo Manthang has been the capital of the Kingdom of Lo (Mustang) since the 14th century, founded by the warrior-king Ame Pal around 1380. Nepal officially abolished its monarchy in 2008, but the current king, Jigme Dorje Palbar Bista, remains deeply revered by the Loba people.',                                                                                                      'type' => 'historical', 'icon_class' => 'fas fa-crown'],
                            ['title' => 'Tiji Festival',   'content' => 'The three-day Tiji (Tenchi) Festival, held in May, is Lo Manthang\'s most spectacular event — a ritualistic masked dance drama performed by monks of Choprang Monastery depicting the defeat of a demon threatening the kingdom. It draws visitors from around the world.',                                                                                                                  'type' => 'cultural',   'icon_class' => 'fas fa-theater-masks'],
                            ['title' => 'The Four Temples', 'content' => 'Lo Manthang contains four ancient temples: Jhampa, Thubchen, Chodey, and Choprang Monastery. Thubchen (15th century) has some of the finest Buddhist murals in the Himalayas, currently being restored with assistance from the American Himalayan Foundation.',                                                                                                                          'type' => 'cultural',   'icon_class' => 'fas fa-torii-gate'],
                            ['title' => 'Sky Burial Sites', 'content' => 'The plateau above Lo Manthang contains traditional sky burial (jhator) grounds where the deceased are offered to vultures in a ceremony central to Vajrayana Buddhist belief. These sites are sacred and private — observe from a respectful distance and never photograph a ceremony.',                                                                                                    'type' => 'cultural',   'icon_class' => 'fas fa-dove'],
                        ],
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════════════════════
            // 5. Gokyo Lakes & Ri Trek
            // ══════════════════════════════════════════════════════════════════
            [
                'name'                => 'Gokyo Lakes & Gokyo Ri Trek',
                'description'         => 'An alternative and arguably more scenic approach to the Everest region, the Gokyo trek takes you through the longest glacier in Nepal — the Ngozumpa Glacier — to a chain of turquoise high-altitude lakes considered sacred by Hindus and Buddhists. The highlight is summiting Gokyo Ri (5,357 m) for one of the finest panoramic views in the entire Himalaya, taking in Everest, Lhotse, Makalu, and Cho Oyu simultaneously. Far fewer crowds than the classic EBC trail.',
                'trek_type'           => 'scenic',
                'tags'                => json_encode(['gokyo', 'lakes', 'everest', 'glacier', 'off-the-beaten-path']),
                'season'              => json_encode(['spring', 'autumn']),
                'region'              => 'Khumbu, Solukhumbu',
                'price'               => 1400.00,
                'duration_days'       => 12,
                'difficulty_level'    => 'hard',
                'max_participants'    => 12,
                'image'               => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=1200',
                'start_location_name' => 'Lukla (Tenzing-Hillary Airport)',
                'start_lat'           => 27.6869,
                'start_lng'           => 86.7314,
                'end_location_name'   => 'Gokyo Ri (5,357 m)',
                'end_lat'             => 27.9625,
                'end_lng'             => 86.6878,
                'is_active'           => true,
                'views_count'         => 810,
                'bookings_count'      => 52,
                'rating_avg'          => 4.89,
                'rating_count'        => 46,
                'checkpoints'         => [
                    [
                        'name'                         => 'Namche Bazaar (3,440 m)',
                        'description'                  => 'The Sherpa capital and the last major supply hub before the high Khumbu. For the Gokyo route, trekkers leave the main EBC trail at Namche and head northwest into the less-travelled Gokyo valley.',
                        'image'                        => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=800',
                        'latitude'                     => 27.8069,
                        'longitude'                    => 86.7139,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 390,
                        'facts' => [
                            ['title' => 'Stock Up in Namche', 'content' => 'The Gokyo valley has fewer and smaller tea houses than the EBC route, and prices increase significantly above Namche. Stock up on snacks, batteries, and any medications you might need. There is a well-equipped pharmacy and several bakeries in Namche.', 'type' => 'tip',      'icon_class' => 'fas fa-shopping-basket'],
                            ['title' => 'Route Divergence',   'content' => 'At Namche the trail splits: the right fork heads toward Tengboche and Everest Base Camp; the left fork follows the Dudh Koshi river northwest to Dole, Machhermo, and Gokyo. The Gokyo route is noticeably quieter and the scenery arguably more dramatic.',    'type' => 'info',     'icon_class' => 'fas fa-code-branch'],
                            ['title' => 'Sherpa Heritage',    'content' => 'The Sherpa people migrated from Kham, Tibet, about 500 years ago and settled the Khumbu region. Their extraordinary high-altitude physiology — including greater lung capacity and higher hemoglobin efficiency — is partly genetic and partly a result of generations of high-altitude living.', 'type' => 'cultural', 'icon_class' => 'fas fa-dna'],
                        ],
                    ],
                    [
                        'name'                         => 'Machhermo (4,470 m)',
                        'description'                  => 'A small village on a high moraine shelf with dramatic views up the Gokyo valley. Machhermo is the standard acclimatisation stop before the final push to the lakes — and has a remarkable yeti sighting in its history.',
                        'image'                        => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800',
                        'latitude'                     => 27.9167,
                        'longitude'                    => 86.6778,
                        'radius'                       => 80,
                        'estimated_time_from_previous' => 360,
                        'facts' => [
                            ['title' => 'The Machhermo Yeti Incident',  'content' => 'In 1974, a Sherpa woman named Lhakpa reported being attacked by a large bipedal creature at Machhermo that killed three of her yaks and left unusual footprints in the snow. Her account was investigated by mountaineer Don Whillans and remains one of the most credible yeti encounter reports in the Himalayas.', 'type' => 'historical', 'icon_class' => 'fas fa-paw'],
                            ['title' => 'HRA Post',                      'content' => 'Like Manang on the Annapurna Circuit, Machhermo has a Himalayan Rescue Association post with a physician during peak seasons. At 4,470 m, altitude sickness is a real risk — attend any available lecture and do not proceed if you have symptoms.',                                                                   'type' => 'safety',     'icon_class' => 'fas fa-stethoscope'],
                            ['title' => 'High Altitude Acclimatisation', 'content' => 'Spend two nights in Machhermo. On your rest day, hike up to the ridge above the village (approximately 5,000 m) for spectacular views of Cho Oyu (8,188 m) — the world\'s sixth-highest peak — directly to the north across the Tibetan border.',                                                                      'type' => 'tip',        'icon_class' => 'fas fa-hiking'],
                        ],
                    ],
                    [
                        'name'                         => 'Gokyo Village & Dudh Pokhari (4,790 m)',
                        'description'                  => 'The third and largest of the six Gokyo Lakes, Dudh Pokhari (Milk Lake) sits beside the village of Gokyo at 4,790 m. The turquoise water reflecting Cho Oyu and the surrounding peaks is one of the most strikingly beautiful scenes in the entire Himalayas.',
                        'image'                        => 'https://images.unsplash.com/photo-1516912481808-3406841bd33c?w=800',
                        'latitude'                     => 27.9614,
                        'longitude'                    => 86.6847,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 180,
                        'facts' => [
                            ['title' => 'Sacred Ramsar Wetland', 'content' => 'The Gokyo Lakes system (six lakes in a chain) is listed as a Ramsar Wetland of International Importance — a globally significant designation recognising the ecological value of the unique high-altitude wetland ecosystem. Swimming or washing in the lakes is strictly prohibited.', 'type' => 'natural',  'icon_class' => 'fas fa-water'],
                            ['title' => 'Ngozumpa Glacier',      'content' => 'Running alongside the trail from below Machhermo to above Gokyo, the Ngozumpa is the longest glacier in Nepal at 36 km. From Gokyo Ri you can see the full extent of its chaotic, debris-covered surface snaking back to the flanks of Cho Oyu.',                                   'type' => 'natural',  'icon_class' => 'fas fa-icicles'],
                            ['title' => 'Hindu Pilgrimage',       'content' => 'During the auspicious festival of Janai Purnima (full moon in August), Hindu pilgrims make a difficult high-altitude pilgrimage to the Gokyo Lakes to bathe in the sacred waters. The combination of trekkers and pilgrims on this day creates a remarkable cultural atmosphere.',  'type' => 'cultural', 'icon_class' => 'fas fa-om'],
                        ],
                    ],
                    [
                        'name'                         => 'Gokyo Ri Summit (5,357 m)',
                        'description'                  => 'The supreme viewpoint of the Gokyo valley, offering what many serious trekkers and mountaineers rank as the finest panoramic view in the entire Himalaya. From the summit you can simultaneously see four of the world\'s six highest mountains: Everest (8,849 m), Lhotse (8,516 m), Makalu (8,485 m), and Cho Oyu (8,188 m).',
                        'image'                        => 'https://images.unsplash.com/photo-1606132399145-ced8d6a7c19e?w=800',
                        'latitude'                     => 27.9625,
                        'longitude'                    => 86.6878,
                        'radius'                       => 80,
                        'estimated_time_from_previous' => 180,
                        'facts' => [
                            ['title' => 'Four 8,000ers at Once',      'content' => 'Gokyo Ri is one of only a handful of points on earth from which you can see four peaks over 8,000 m simultaneously: Everest (8,849 m), Lhotse (8,516 m), Makalu (8,485 m), and Cho Oyu (8,188 m). Many experienced Himalayan guides consider this a more impressive view than from Kala Patthar.', 'type' => 'natural', 'icon_class' => 'fas fa-binoculars'],
                            ['title' => 'Summit Conditions',           'content' => 'The ascent of Gokyo Ri from the village takes 2–3 hours and gains approximately 570 m. The trail is steep and rocky but non-technical. Start before 06:00 to avoid afternoon cloud build-up and high winds. Trekking poles are highly recommended.',                                               'type' => 'safety',  'icon_class' => 'fas fa-exclamation-triangle'],
                            ['title' => 'The Cho Oyu Base Camp Route', 'content' => 'The valley above Gokyo continues to the fifth and sixth lakes and beyond to the base of Cho Oyu — the world\'s sixth highest mountain. Experienced trekkers with extra days can extend to the Cho Oyu Base Camp at 5,700 m, a permit-free and wildly beautiful add-on.',                         'type' => 'tip',     'icon_class' => 'fas fa-route'],
                            ['title' => 'Everest\'s True Height',      'content' => 'In 2020, a joint China-Nepal survey revised Everest\'s official height from 8,848 m to 8,848.86 m — typically rounded to 8,849 m. This resolved a long-standing discrepancy between Chinese and Nepalese measurements and is now the globally accepted figure.',                                  'type' => 'info',    'icon_class' => 'fas fa-ruler-vertical'],
                        ],
                    ],
                ],
            ],

        ]; // end packages array
    }
}