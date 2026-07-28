<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\TourPackage;
use App\Models\Checkpoint;
use App\Models\CheckpointFact;

class ChitwanPokharaSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->packages() as $pkgData) {
            $checkpoints = $pkgData['checkpoints'];
            $hotels      = $pkgData['hotels'] ?? [];
            unset($pkgData['checkpoints'], $pkgData['hotels']);

            $package = TourPackage::firstOrCreate(
                ['name' => $pkgData['name']],  
                $pkgData                        
            );

            if (!$package->wasRecentlyCreated) {
                $this->command?->info("Skipped (already exists): {$pkgData['name']}");
                continue;
            }

            $createdCheckpoints = [];

            foreach ($checkpoints as $order => $cpData) {
                $facts = $cpData['facts'];
                unset($cpData['facts']);

                $cpData['tour_package_id'] = $package->id;
                $cpData['order']           = $order + 1;

                $checkpoint = Checkpoint::create($cpData);
                $createdCheckpoints[] = $checkpoint;

                foreach ($facts as $factOrder => $factData) {
                    CheckpointFact::create(array_merge($factData, [
                        'checkpoint_id' => $checkpoint->id,
                        'order'         => $factOrder + 1,
                    ]));
                }
            }

            // Add hotels as regular checkpoints
            foreach ($hotels as $hotelOrder => $hotelData) {
                $facts = $hotelData['facts'];
                unset($hotelData['facts']);

                $hotelData['tour_package_id'] = $package->id;
                $hotelData['order']           = count($checkpoints) + $hotelOrder + 1;

                $checkpoint = Checkpoint::create($hotelData);
                $createdCheckpoints[] = $checkpoint;

                foreach ($facts as $factOrder => $factData) {
                    CheckpointFact::create(array_merge($factData, [
                        'checkpoint_id' => $checkpoint->id,
                        'order'         => $factOrder + 1,
                    ]));
                }
            }

            $this->command?->info("✅ Created: {$pkgData['name']} with " . (count($checkpoints) + count($hotels)) . " checkpoints");
        }
    }

    private function packages(): array
    {
        return [
           
            [
                'name'                => 'Chitwan National Park Jungle Safari',
                'description'         => 'Nepal\'s first national park and a UNESCO World Heritage Site, Chitwan offers a completely different side of Nepal — steamy lowland jungle, subtropical grassland, and some of the best wildlife viewing in South Asia. Track one-horned rhinos on jeep safaris, cruise the Rapti River on traditional dugout canoes, and spend an evening with the indigenous Tharu community.',
                'trek_type'           => 'wildlife',
                'tags'                => json_encode(['chitwan', 'wildlife', 'safari', 'jungle', 'unesco', 'rhino']),
                'season'              => json_encode(['winter', 'spring', 'autumn']),
                'region'              => 'Chitwan, Bagmati',
                'price'               => 25000.00,
                'duration_days'       => 4,
                'difficulty_level'    => 'easy',
                'max_participants'    => 20,
                'image'               => 'https://imgs.search.brave.com/_1-GtpfiPDhJ43kakCcpFA0BBxeAEA1PWlSELekv52A/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9waG90/b3Muc211Z211Zy5j/b20vQXNpYS9OZXBh/bC10cmF2ZWwvaS16/OHJOSDRjLzAvWDIv/Y2hpdHdhbi1uYXRp/b25hbC1wYXJrLTMt/WDIuanBn',
                'start_location_name' => 'Sauraha',
                'start_lat'           => 27.5817,
                'start_lng'           => 84.5011,
                'end_location_name'   => 'Kasara',
                'end_lat'             => 27.5461,
                'end_lng'             => 84.3956,
                'is_active'           => true,
                'views_count'         => 540,
                'bookings_count'      => 33,
                'rating_avg'          => 4.72,
                'rating_count'        => 28,

                'hotels' => [
                    [
                        'name'                         => 'Rhino Lodge Chitwan',
                        'description'                  => 'A riverside eco-lodge on the edge of Sauraha, built in traditional Tharu style with thatched roofs and mud-plaster walls, offering easy access to the park entrance and canoe launch point.',
                        'image'                        => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800',
                        'latitude'                     => 27.5820,
                        'longitude'                    => 84.5030,
                        'radius'                       => 120,
                        'estimated_time_from_previous' => 0,
                        'facts' => [
                            ['title' => 'Stay in the Jungle Edge', 'content' => 'A comfortable stay near the park entrance with easy access to safaris and river activities.', 'type' => 'info', 'icon_class' => 'fas fa-hotel'],
                            ['title' => 'Traditional Tharu Style', 'content' => 'Designed with local architecture and a nature-focused atmosphere.', 'type' => 'cultural', 'icon_class' => 'fas fa-home'],
                        ],
                    ],
                ],

                'checkpoints' => [
                    [
                        'name'                         => 'Sauraha — Gateway to Chitwan',
                        'description'                  => 'The bustling tourist village on the northern edge of Chitwan National Park. Sauraha is packed with lodges, souvenir stalls, and the launch point for jeep safaris, canoe rides, and jungle walks into the park.',
                        'image'                        => 'https://imgs.search.brave.com/1RZJB57s31rnGxNSJypt-5YOx2Xq3bMmP2KM-RdMpjg/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9tZWRp/YS5nZXR0eWltYWdl/cy5jb20vaWQvNTIz/OTg3NTY5L3Bob3Rv/L3R1cmlzdC1jcm9z/c2luZy1yYXB0aS1y/aXZlci1pbi1zYXVy/YWhhLXZpbGxhZ2Uu/anBnP3M9NjEyeDYx/MiZ3PTAmaz0yMCZj/PUlnSlRHeU9uajdI/TUV0WUF5bjZXMFhG/R25JVmhHZi1jSzJk/VEJhYUZYSnM9',
                        'latitude'                     => 27.5817,
                        'longitude'                    => 84.5011,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 0,
                        'facts' => [
                            ['title' => 'UNESCO World Heritage Site', 'content' => 'Chitwan National Park was established in 1973 and inscribed as a UNESCO World Heritage Site in 1984 for its exceptional biodiversity.', 'type' => 'historical', 'icon_class' => 'fas fa-landmark'],
                            ['title' => 'Main Tourist Hub', 'content' => 'Sauraha is the main base for most Chitwan visitors, with easy access to park activities.', 'type' => 'info', 'icon_class' => 'fas fa-map-marker-alt'],
                        ],
                    ],
                    [
                        'name'                         => 'Elephant Breeding Center — Khorsor',
                        'description'                  => 'A government-run sanctuary just outside Sauraha dedicated to breeding and caring for domesticated Asian elephants.',
                        'image'                        => 'https://imgs.search.brave.com/s4NYC3996cKF5JWQaeUQdzR9essFAbtMdEoqZgBgHjg/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9tZWRp/YS5pc3RvY2twaG90/by5jb20vaWQvMTE2/NjU0MDY5Mi9waG90/by9jaGl0d2FuLW5h/dGlvbmFsLXBhcmst/aW4tbmVwYWwuanBn/P3M9NjEyeDYxMiZ3/PTAmaz0yMCZjPUJC/SHEtR0NqN2F2M2lX/aHZoc1p6Tl9wMkhl/cFhzUWhlSzZiNjAx/M3cyekE9',
                        'latitude'                     => 27.5764,
                        'longitude'                    => 84.4877,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 30,
                        'facts' => [
                            ['title' => 'Elephant Conservation', 'content' => 'The center focuses on caring for and breeding elephants used in conservation and park support work.', 'type' => 'historical', 'icon_class' => 'fas fa-paw'],
                            ['title' => 'Ethical Viewing', 'content' => 'Observe responsibly from designated viewing areas.', 'type' => 'tip', 'icon_class' => 'fas fa-heart'],
                        ],
                    ],
                    [
                        'name'                         => 'Rapti River Canoe Ride',
                        'description'                  => 'A serene ride down the Rapti River in a traditional dugout canoe. Glide silently past crocodiles and birdlife along the banks.',
                        'image'                        => 'https://imgs.search.brave.com/K3bMhIzE9H01qetf8enJxZG_jmAghUbpIGgaqE7p1jg/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly93d3cu/Y2hpdHdhbnRvdXJp/c20uY29tL3dwLWNv/bnRlbnQvdXBsb2Fk/cy8yMDI0LzAxL2Nh/bm9laW5nLWJvYXRp/bmctZ2FsbGVyeS00/LmpwZw',
                        'latitude'                     => 27.5769,
                        'longitude'                    => 84.4978,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 45,
                        'facts' => [
                            ['title' => 'Gharial Habitat', 'content' => 'The Rapti River is home to the critically endangered gharial crocodile.', 'type' => 'natural', 'icon_class' => 'fas fa-water'],
                            ['title' => 'Quiet Wildlife Viewing', 'content' => 'Canoes allow close but quiet viewing of river wildlife.', 'type' => 'tip', 'icon_class' => 'fas fa-binoculars'],
                        ],
                    ],
                    [
                        'name'                         => 'Jungle Jeep Safari — Core Park Area',
                        'description'                  => 'A 4-hour open-jeep safari deep into the park\'s core grassland and sal forest, tracking rhinos, deer, boar, and possibly tiger.',
                        'image'                        => 'https://imgs.search.brave.com/XOKWsVBOOns7H_RCUFPEpEHN3KdiULn3A4GpctIwu7M/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9tZWRp/YS5nZXR0eWltYWdl/cy5jb20vaWQvMTI5/MzQ2MjI4OC9waG90/by9oYXBweS15b3Vu/Zy13b21hbi1vbi1s/dXh1cnktc2FmYXJp/LWxvb2tpbmctYXQt/d2lsbC1lbGVwaGFu/dC13YWxraW5nLWlu/LXRoZS1qdW5nbGUu/anBnP3M9NjEyeDYx/MiZ3PTAmaz0yMCZj/PU5IWnFGb2tvSkNj/S2UwcE9pSkNlTnd3/emtTVUhQWmxiNDhZ/OXV0S1NhV1k9',
                        'latitude'                     => 27.5450,
                        'longitude'                    => 84.4700,
                        'radius'                       => 300,
                        'estimated_time_from_previous' => 90,
                        'facts' => [
                            ['title' => 'Rhino Country', 'content' => 'Chitwan is famous for its strong population of greater one-horned rhinoceros.', 'type' => 'historical', 'icon_class' => 'fas fa-paw'],
                            ['title' => 'Safari Etiquette', 'content' => 'Stay seated and keep noise low when animals are nearby.', 'type' => 'safety', 'icon_class' => 'fas fa-exclamation-triangle'],
                        ],
                    ],
                    [
                        'name'                         => 'Bees Hazari Tal — 20,000 Lakes',
                        'description'                  => 'A tranquil oxbow lake system inside the buffer zone, popular for birdwatching and peaceful walks.',
                        'image'                        => 'https://imgs.search.brave.com/3EerdLYQeY3svp1TT67lY0HrdfMAtNonCcECV6Fzpww/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9tZWRp/YS5nZXR0eWltYWdl/cy5jb20vaWQvOTky/OTk3OTA0L3Bob3Rv/L21haG91dHMtY3Jv/c3NpbmctdGhlLWVh/c3QtcmFwdGktcml2/ZXItd2l0aC10aGVp/ci1lbGVwaGFudHMt/YXQtc2F1cmFoYS1u/ZWFyLXRoZS1jaGl0/d2FuLmpwZz9zPTYx/Mng2MTImdz0wJms9/MjAmYz1PWURLaEtn/VXZzVXVoTkQ0aHRB/VTZZc3pvYjhiWEFo/M3NEbVFYWjU3eFAw/PQ',
                        'latitude'                     => 27.6142,
                        'longitude'                    => 84.4189,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 60,
                        'facts' => [
                            ['title' => 'Wetland Area', 'content' => 'A quiet wetland zone with waterbirds and calm scenery.', 'type' => 'natural', 'icon_class' => 'fas fa-water'],
                            ['title' => 'Less Crowded Stop', 'content' => 'A slower, quieter alternative to the main safari routes.', 'type' => 'tip', 'icon_class' => 'fas fa-walking'],
                        ],
                    ],
                    [
                        'name'                         => 'Tharu Village & Cultural Museum',
                        'description'                  => 'A visit to a traditional Tharu village to learn about the indigenous people of the Chitwan lowlands, followed by an evening cultural performance.',
                        'image'                        => 'https://imgs.search.brave.com/fPyplkjUqNtBzA0KZxHthAj3QG-5WHgZGmKsOx9NBzo/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9hZHZl/bnR1cmV3b3JsZHRy/YXZlbHMuY29tL3dw/LWNvbnRlbnQvdXBs/b2Fkcy8yMDI2LzA2/L3RoYXJ1LWN1bHR1/cmUtaW4tY2hpdHdh/bi1uZXBhbC53ZWJw',
                        'latitude'                     => 27.5900,
                        'longitude'                    => 84.5100,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 45,
                        'facts' => [
                            ['title' => 'Tharu Heritage', 'content' => 'The Tharu people have lived in the Chitwan lowlands for generations.', 'type' => 'cultural', 'icon_class' => 'fas fa-users'],
                            ['title' => 'Stick Dance', 'content' => 'Tharu cultural performances are one of the highlights of the region.', 'type' => 'cultural', 'icon_class' => 'fas fa-drum'],
                        ],
                    ],
                    [
                        'name'                         => 'Kasara — Gharial Breeding Centre',
                        'description'                  => 'The administrative headquarters of Chitwan National Park, home to a gharial crocodile breeding and rehabilitation centre.',
                        'image'                        => 'https://imgs.search.brave.com/h7bafSXDxbRbXBVQjNfS694FyLIsUhkZiWY0qE_9ISo/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9hc3Nl/dHMtYXBpLmthdGht/YW5kdXBvc3QuY29t/L3RodW1iLnBocD9z/cmM9aHR0cHM6Ly9h/c3NldHMtY2RuLmth/dGhtYW5kdXBvc3Qu/Y29tL3VwbG9hZHMv/c291cmNlL25ld3Mv/MjAxOS9taXNjZWxs/YW5lb3VzL3NodXR0/ZXJzdG9ja18xMzA4/NjY1NDkxLTIwMDYy/MDE5MDgzNDA4Lmpw/ZyZ3PTkwMCZoZWln/aHQ9NjAx',
                        'latitude'                     => 27.5461,
                        'longitude'                    => 84.3956,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 75,
                        'facts' => [
                            ['title' => 'Park Headquarters', 'content' => 'Kasara houses the park\'s administrative offices and natural history display.', 'type' => 'info', 'icon_class' => 'fas fa-building'],
                            ['title' => 'Gharial Breeding', 'content' => 'The centre helps protect and release gharials back into the wild.', 'type' => 'historical', 'icon_class' => 'fas fa-water'],
                        ],
                    ],
                ],
            ],

            [
                'name'                => 'Pokhara Lakeside & Sarangkot Sunrise',
                'description'         => 'Nepal\'s most famous tourist city after Kathmandu, Pokhara is renowned for the tranquil beauty of Phewa Lake set against the towering backdrop of the Annapurna and Machhapuchhre ranges. This gentle getaway combines lakeside relaxation, cave and waterfall exploration, temple visits, adventure activities, and one of the most celebrated sunrise viewpoints in the Himalayas at Sarangkot.',
                'trek_type'           => 'scenic',
                'tags'                => json_encode(['pokhara', 'lake', 'sarangkot', 'annapurna-view', 'paragliding', 'bungee']),
                'season'              => json_encode(['spring', 'autumn', 'winter']),
                'region'              => 'Pokhara, Gandaki',
                'price'               => 18000.00,
                'duration_days'       => 3,
                'difficulty_level'    => 'easy',
                'max_participants'    => 25,
                'image'               => 'https://images.unsplash.com/photo-1610997686651-98492fd08108?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8UG9raGFyYXxlbnwwfHwwfHx8MA%3D%3D',
                'start_location_name' => 'Pokhara Lakeside',
                'start_lat'           => 28.2096,
                'start_lng'           => 83.9856,
                'end_location_name'   => 'Sarangkot Viewpoint',
                'end_lat'             => 28.2417,
                'end_lng'             => 83.9622,
                'is_active'           => true,
                'views_count'         => 690,
                'bookings_count'      => 47,
                'rating_avg'          => 4.81,
                'rating_count'        => 41,

                'hotels' => [
                    [
                        'name'                         => 'Fishtail Lakeview Hotel',
                        'description'                  => 'A comfortable boutique hotel directly on Phewa Lake\'s eastern shore, with private balconies overlooking the water and the Annapurna range, walking distance to Lakeside\'s restaurants and boat docks.',
                        'image'                        => 'https://imgs.search.brave.com/iRSdKWdzhlt6V85ldcCACQAmcOZMwtCG5N3MoUrf_Gs/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly93d3cu/YW1wZXJzYW5kdHJh/dmVsLmNvbS9tZWRp/YS82MTE3My9GaXNo/dGFpbC1Mb2RnZS1Q/b2toYXJhLU5lcGFs/LTEtLmpwZz9tb2Rl/PWNyb3AmcXVhbGl0/eT05NSZ3aWR0aD04/MjAmaGVpZ2h0PTUz/NA',
                        'latitude'                     => 28.2101,
                        'longitude'                    => 83.9581,
                        'radius'                       => 120,
                        'estimated_time_from_previous' => 0,
                        'facts' => [
                            ['title' => 'Lakeside Stay', 'content' => 'A peaceful hotel stay near Phewa Lake with mountain views.', 'type' => 'info', 'icon_class' => 'fas fa-hotel'],
                            ['title' => 'Close to Attractions', 'content' => 'Easy access to Lakeside restaurants, boat docks, and city highlights.', 'type' => 'tip', 'icon_class' => 'fas fa-map-marker-alt'],
                        ],
                    ],
                ],

                'checkpoints' => [
                    [
                        'name'                         => 'Phewa Lake & Tal Barahi Temple',
                        'description'                  => 'The second-largest lake in Nepal, Phewa Lake is Pokhara\'s centrepiece — a mirror-still body of water reflecting the Annapurna range on clear mornings. At its heart sits Tal Barahi, a two-storey pagoda temple accessible only by boat.',
                        'image'                        => 'https://imgs.search.brave.com/3eM51AeVYQN8Lx6zooBvMCioD3h015oIhcpOyaaB414/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly90aHVt/YnMuZHJlYW1zdGlt/ZS5jb20vYi90YWwt/YmFyYWhpLXRlbXBs/ZS1waGV3YS1sYWtl/LXBva2hhcmEtbmVw/YWwtdGFhbC1taWRk/bGUtNjc1Njk0MDcu/anBn',
                        'latitude'                     => 28.2096,
                        'longitude'                    => 83.9575,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 0,
                        'facts' => [
                            ['title' => 'Second-Largest Lake in Nepal', 'content' => 'Phewa Lake covers approximately 4.4 square kilometres and is one of the most photographed places in Nepal.', 'type' => 'natural', 'icon_class' => 'fas fa-water'],
                            ['title' => 'Tal Barahi Temple', 'content' => 'Built on a small island, the temple is reached by boat and is a major religious site.', 'type' => 'historical', 'icon_class' => 'fas fa-place-of-worship'],
                        ],
                    ],
                                        [
                        'name'                         => 'Davis Falls & Gupteshwor Cave',
                        'description'                  => 'A dramatic waterfall paired with a sacred limestone cave containing a natural Shiva lingam.',
                        'image'                        => 'https://imgs.search.brave.com/xfOEeQHQXc8KEIRhrbXHkEVGA1pVUBQd4w_ZXeEw3es/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9pbWcu/dHJhdmVsdHJpYW5n/bGUuY29tL2Jsb2cv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvRGF2aXMtRmFs/bHMuanBn',
                        'latitude'                     => 28.1928,
                        'longitude'                    => 83.9531,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 30,
                        'facts' => [
                            ['title' => 'The Legend Behind the Name', 'content' => 'Davis Falls (locally "Patale Chhango") is named after a Swiss tourist who reportedly drowned here in 1961 after being swept into the sinkhole while swimming.', 'type' => 'historical', 'icon_class' => 'fas fa-water'],
                            ['title' => 'Gupteshwor Mahadev Cave', 'content' => 'This limestone cave houses a natural stone Shiva lingam and offers a dramatic vantage point looking directly up at Davis Falls.', 'type' => 'natural', 'icon_class' => 'fas fa-mountain'],
                        ],
                    ],
                    [
                        'name'                         => 'World Peace Pagoda (Shanti Stupa)',
                        'description'                  => 'A gleaming white Buddhist stupa perched on a ridge above Phewa Lake, built as a monument to world peace.',
                        'image'                        => 'https://imgs.search.brave.com/-m-xLzWW0VGvN0b0OmzuPSLlHh4yvokJn26ly71k46A/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly93d3cu/ZWFzeXRvdXJuZXBh/bC5jb20vYWRtaW4v/cHVibGljL2ltYWdl/cy9wb3N0L3dvcmxk/LXBlYWNlLXBhZ29k/YS1wb2toYXJhLnBu/Zw',
                        'latitude'                     => 28.2001,
                        'longitude'                    => 83.9486,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 60,
                        'facts' => [
                            ['title' => 'Built by Japanese Buddhist Monks', 'content' => 'The Shanti Stupa was built by the Japanese organisation Nipponzan Myohoji.', 'type' => 'historical', 'icon_class' => 'fas fa-torii-gate'],
                            ['title' => 'Best Panoramic Viewpoint', 'content' => 'From the stupa\'s terrace you get a sweeping 180° view of Pokhara, the lake, and the Annapurna range.', 'type' => 'natural', 'icon_class' => 'fas fa-binoculars'],
                        ],
                    ],
                    [
                        'name'                         => 'Bindhyabasini Temple',
                        'description'                  => 'A historic hilltop temple in Pokhara\'s old bazaar area dedicated to Goddess Bhagwati.',
                        'image'                        => 'https://imgs.search.brave.com/De6MXwIa_jSkIWJe185u67F8k0eyW7Dvqe3ysbDEUUI/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly93d3cu/bGFuZG5lcGFsLmNv/bS93cC1jb250ZW50/L3VwbG9hZHMvMjAy/MS8wNy9CaW5kaHlh/YmFzaW5pLVRlbXBs/ZS5qcGc',
                        'latitude'                     => 28.2317,
                        'longitude'                    => 83.9781,
                        'radius'                       => 80,
                        'estimated_time_from_previous' => 35,
                        'facts' => [
                            ['title' => 'Centuries-Old Devotion', 'content' => 'Bindhyabasini has served as a place of worship for generations.', 'type' => 'historical', 'icon_class' => 'fas fa-place-of-worship'],
                            ['title' => 'Local Rituals', 'content' => 'The temple remains an active religious site for local devotees.', 'type' => 'cultural', 'icon_class' => 'fas fa-pray'],
                        ],
                    ],
                    [
                        'name'                         => 'Paragliding Launch Point — Sarangkot',
                        'description'                  => 'One of the world\'s top paragliding launch sites, overlooking Phewa Lake and the Annapurna range.',
                        'image'                        => 'https://imgs.search.brave.com/i-4xvXWW5CLuWXTtE4tTzCJ1L8o0mEA5LJLDnizjb24/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly93d3cu/bW91bnRleHBlcmll/bmNlLmNvbS93cC1j/b250ZW50L3VwbG9h/ZHMvMjAxOS8wMy9Q/YXJhZ2xpZGluZy1m/cm9tLVNhcmFuZ2tv/dC01MDB4MzUwLmpw/Zw',
                        'latitude'                     => 28.2417,
                        'longitude'                    => 83.9622,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 50,
                        'facts' => [
                            ['title' => 'Paragliding Capital of Nepal', 'content' => 'Sarangkot is one of Nepal\'s most popular paragliding launch sites due to its steady thermals and mountain views.', 'type' => 'info', 'icon_class' => 'fas fa-parachute-box'],
                            ['title' => 'Adventure Activity', 'content' => 'A perfect add-on for travellers looking for a thrill in Pokhara.', 'type' => 'tip', 'icon_class' => 'fas fa-wind'],
                        ],
                    ],
                    [
                        'name'                         => 'Bungee Jumping — Kushma Side Trip',
                        'description'                  => 'A thrilling adventure stop for travellers wanting to add a bungee jumping experience to their Pokhara trip.',
                        'image'                        => 'https://imgs.search.brave.com/UZDtr71z_C43FkqzCRzoX9Ui8LcTCv5B4-DQvg3-d-A/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9jZG4u/Z2V0eW91cmd1aWRl/LmNvbS9pbWFnZS9m/b3JtYXQ9YXV0byxm/aXQ9Y292ZXIsZ3Jh/dml0eT1hdXRvLHF1/YWxpdHk9NjAsd2lk/dGg9MjcwLGhlaWdo/dD0xODAsZHByPTIv/dG91cl9pbWcvYzJl/N2M4ZWEwNjgyOTY3/MC5qcGVn',
                        'latitude'                     => 28.2333,
                        'longitude'                    => 83.9833,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 90,
                        'facts' => [
                            ['title' => 'Extreme Adventure', 'content' => 'Bungee jumping is one of the most exciting adventure add-ons around Pokhara.', 'type' => 'info', 'icon_class' => 'fas fa-arrow-down'],
                            ['title' => 'For Thrill Seekers', 'content' => 'Best suited for visitors who want a high-adrenaline activity during the trip.', 'type' => 'tip', 'icon_class' => 'fas fa-heart-pulse'],
                        ],
                    ],
                    [
                        'name'                         => 'Begnas Lake',
                        'description'                  => 'A quieter, less-visited sister lake to Phewa, ringed by forested hills and terraced farmland.',
                        'image'                        => 'https://imgs.search.brave.com/ozdPLxCRfweBMzSjos46QOXCvHKaumhSo9xHyzRZkKg/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly93d3cu/bWVlci5jb20vYXR0/YWNobWVudHMvZTIz/ODlkMGNhZjRiMmI2/OTk3ZWQ1N2FmMTRk/MzI2NTAzM2YxYjgw/Yi9zdG9yZS9maWxs/LzQxMC8zMDgvMWFi/YjNjNDliYTc0OTI1/YWY3ZDcyYWQ4MjVh/ZGNjMDI3YjVjOTg1/ZmI3ZGNlMjNmNDI3/ZDVkZWVhOGI2L0Jl/Z25hcy1MYWtlLXNp/dHVhdGVkLWluLUxl/a2huYXRoLUthc2tp/LURpc3RyaWN0LU5l/cGFsLWlzLWEtcG9w/dWxhci1kZXN0aW5h/dGlvbi1mb3Itc2xv/dy10b3VyaXNtLmpw/Zw',
                        'latitude'                     => 28.1892,
                        'longitude'                    => 84.0281,
                        'radius'                       => 150,
                        'estimated_time_from_previous' => 45,
                        'facts' => [
                            ['title' => 'Peaceful Lake Stop', 'content' => 'Begnas Lake is ideal for boating, fishing, and relaxed lakeside time.', 'type' => 'natural', 'icon_class' => 'fas fa-water'],
                            ['title' => 'Less Crowded', 'content' => 'A quieter alternative to the main Pokhara tourist zone.', 'type' => 'tip', 'icon_class' => 'fas fa-leaf'],
                        ],
                    ],
                    [
                        'name'                         => 'Sarangkot Sunrise Viewpoint',
                        'description'                  => 'The most famous sunrise viewpoint near Pokhara, with panoramic Himalayan views including Machhapuchhre and Annapurna.',
                        'image'                        => 'https://imgs.search.brave.com/ZlCe43BJaII1OnRjc_J_RcBcQrz8BMJ8AbupoGd-KRI/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9qb25p/c3RyYXZlbGxpbmcu/Y29tL3dwLWNvbnRl/bnQvdXBsb2Fkcy8y/MDE0LzEwL3N1bnJp/c2UtaW4tc2FyYW5n/a290LWUxNDQxMDMw/NTExNjE1LmpwZw',
                        'latitude'                     => 28.2417,
                        'longitude'                    => 83.9622,
                        'radius'                       => 100,
                        'estimated_time_from_previous' => 50,
                        'facts' => [
                            ['title' => 'Iconic Sunrise', 'content' => 'Sarangkot is one of the best sunrise viewpoints in Nepal.', 'type' => 'natural', 'icon_class' => 'fas fa-sun'],
                            ['title' => 'Mountain Panorama', 'content' => 'It offers clear views of Dhaulagiri, Annapurna, and Machhapuchhre.', 'type' => 'info', 'icon_class' => 'fas fa-mountain'],
                        ],
                    ],
                ],
            ],
        ];
    }
}