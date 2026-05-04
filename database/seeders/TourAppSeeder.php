<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TourPackage;
use App\Models\Checkpoint;
use App\Models\CheckpointFact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TourAppSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@paaila.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '9841234567',
        ]);

        $traveler = User::create([
            'name' => 'Ram Sharma',
            'email' => 'ram@example.com',
            'password' => Hash::make('password'),
            'role' => 'traveler',
            'phone' => '9851234567',
            'address' => 'Kathmandu, Nepal',
        ]);

        $this->command->info('Users created: admin@tourapp.com / password, ram@example.com / password');

        $package1 = TourPackage::create([
            'name' => 'Kathmandu Valley Heritage Tour',
            'description' => 'Explore the ancient temples and cultural sites of Kathmandu Valley. Visit UNESCO World Heritage Sites including Swayambhunath, Pashupatinath, and Boudhanath. Experience the rich history and spirituality of Nepal.',
            'price' => 8500.00,
            'duration_days' => 2,
            'difficulty_level' => 'easy',
            'max_participants' => 15,
            'start_location_name' => 'Thamel, Kathmandu',
            'start_lat' => 27.7172,
            'start_lng' => 85.3240,
            'end_location_name' => 'Bhaktapur Durbar Square',
            'end_lat' => 27.6710,
            'end_lng' => 85.4298,
            'is_active' => true,
        ]);

        $checkpoint1_1 = Checkpoint::create([
            'tour_package_id' => $package1->id,
            'name' => 'Swayambhunath (Monkey Temple)',
            'description' => 'Ancient Buddhist stupa with panoramic valley views',
            'order' => 1,
            'latitude' => 27.7149,
            'longitude' => 85.2906,
            'radius' => 100,
            'estimated_time_from_previous' => 15,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_1->id,
            'title' => 'Historical Significance',
            'content' => 'Swayambhunath is over 2,500 years old, making it one of the oldest Buddhist stupas in Nepal. Legend says it sprang up spontaneously when the valley was created from a primordial lake.',
            'type' => 'history',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_1->id,
            'title' => 'The All-Seeing Eyes',
            'content' => 'The eyes painted on all four sides of the stupa represent the all-seeing eyes of Buddha. The nose is actually the Nepali number one, symbolizing unity.',
            'type' => 'culture',
            'order' => 2,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_1->id,
            'title' => 'Watch Out for Monkeys!',
            'content' => 'The temple is home to hundreds of holy monkeys. Keep your belongings secure and avoid feeding them. They are considered sacred guardians of the temple.',
            'type' => 'safety',
            'order' => 3,
        ]);

        $checkpoint1_2 = Checkpoint::create([
            'tour_package_id' => $package1->id,
            'name' => 'Pashupatinath Temple',
            'description' => 'Sacred Hindu temple complex on the banks of Bagmati River',
            'order' => 2,
            'latitude' => 27.7104,
            'longitude' => 85.3484,
            'radius' => 100,
            'estimated_time_from_previous' => 30,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_2->id,
            'title' => 'Holiest Shiva Temple',
            'content' => 'Pashupatinath is one of the most sacred temples dedicated to Lord Shiva. It attracts thousands of pilgrims from Nepal and India, especially during Maha Shivaratri.',
            'type' => 'history',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_2->id,
            'title' => 'Cremation Ghats',
            'content' => 'The temple complex includes cremation ghats where Hindu cremation ceremonies are performed. This sacred ritual has been practiced here for centuries.',
            'type' => 'culture',
            'order' => 2,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_2->id,
            'title' => 'Dress Modestly',
            'content' => 'This is an active religious site. Dress conservatively (cover shoulders and knees). Non-Hindus cannot enter the main temple, but can observe from the opposite bank.',
            'type' => 'safety',
            'order' => 3,
        ]);

        $checkpoint1_3 = Checkpoint::create([
            'tour_package_id' => $package1->id,
            'name' => 'Boudhanath Stupa',
            'description' => 'Largest spherical stupa in Nepal and center of Tibetan Buddhism',
            'order' => 3,
            'latitude' => 27.7215,
            'longitude' => 85.3624,
            'radius' => 100,
            'estimated_time_from_previous' => 20,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_3->id,
            'title' => 'Tibetan Buddhist Hub',
            'content' => 'Boudhanath is the center of Tibetan Buddhism in Nepal. After the 1959 Tibetan uprising, many refugees settled here, creating a vibrant Tibetan community.',
            'type' => 'history',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_3->id,
            'title' => 'Prayer Wheels & Mantras',
            'content' => 'Walk clockwise around the stupa and spin the prayer wheels. Each turn is believed to have the same effect as reciting the prayers written inside.',
            'type' => 'culture',
            'order' => 2,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint1_3->id,
            'title' => 'Best Viewing Time',
            'content' => 'Visit during early morning or evening prayers for a spiritual experience. The stupa is beautifully lit at night. Many cafes around offer great viewpoints.',
            'type' => 'info',
            'order' => 3,
        ]);

        $package2 = TourPackage::create([
            'name' => 'Pokhara Lakeside Adventure',
            'description' => 'Discover the natural beauty of Pokhara - the gateway to the Himalayas. Enjoy boating on Phewa Lake, visit World Peace Pagoda, explore mysterious caves, and witness stunning mountain views.',
            'price' => 12000.00,
            'duration_days' => 3,
            'difficulty_level' => 'moderate',
            'max_participants' => 20,
            'start_location_name' => 'Lakeside, Pokhara',
            'start_lat' => 28.2096,
            'start_lng' => 83.9596,
            'end_location_name' => 'Sarangkot Viewpoint',
            'end_lat' => 28.2448,
            'end_lng' => 83.9533,
            'is_active' => true,
        ]);

        $checkpoint2_1 = Checkpoint::create([
            'tour_package_id' => $package2->id,
            'name' => 'Phewa Lake',
            'description' => 'Serene lake with Annapurna reflections',
            'order' => 1,
            'latitude' => 28.2096,
            'longitude' => 83.9596,
            'radius' => 150,
            'estimated_time_from_previous' => 10,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint2_1->id,
            'title' => 'Second Largest Lake',
            'content' => 'Phewa Lake is the second largest lake in Nepal. On clear days, you can see perfect reflections of Machhapuchhre (Fishtail Mountain) in its waters.',
            'type' => 'info',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint2_1->id,
            'title' => 'Tal Barahi Temple',
            'content' => 'In the middle of the lake sits a small island with Tal Barahi Temple, dedicated to Goddess Barahi. Take a colorful wooden boat to visit this sacred site.',
            'type' => 'culture',
            'order' => 2,
        ]);

        $checkpoint2_2 = Checkpoint::create([
            'tour_package_id' => $package2->id,
            'name' => 'World Peace Pagoda',
            'description' => 'Buddhist stupa on hilltop with panoramic views',
            'order' => 2,
            'latitude' => 28.2199,
            'longitude' => 83.9431,
            'radius' => 100,
            'estimated_time_from_previous' => 45,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint2_2->id,
            'title' => 'Symbol of Peace',
            'content' => 'Built by Japanese Buddhist monks, the World Peace Pagoda is one of 80 peace pagodas worldwide. It offers 360-degree views of the Annapurna range, Phewa Lake, and Pokhara city.',
            'type' => 'history',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint2_2->id,
            'title' => 'Hiking Trail',
            'content' => 'You can reach here by boat across Phewa Lake followed by a 45-minute uphill hike, or drive around. The hike is moderate and rewarding with stunning views.',
            'type' => 'info',
            'order' => 2,
        ]);

        $checkpoint2_3 = Checkpoint::create([
            'tour_package_id' => $package2->id,
            'name' => 'Devi\'s Fall (Patale Chhango)',
            'description' => 'Unique waterfall that disappears into underground tunnel',
            'order' => 3,
            'latitude' => 28.1900,
            'longitude' => 83.9593,
            'radius' => 80,
            'estimated_time_from_previous' => 35,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint2_3->id,
            'title' => 'Tragic Legend',
            'content' => 'Named after a Swiss tourist "Devi" who tragically fell into the waterfall in 1961. The water creates an underground tunnel that flows for several hundred meters.',
            'type' => 'history',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint2_3->id,
            'title' => 'Gupteshwor Cave',
            'content' => 'Directly opposite is Gupteshwor Mahadev Cave, one of the longest caves in Nepal. You can see Devi\'s Fall from inside the cave - a unique perspective!',
            'type' => 'info',
            'order' => 2,
        ]);

        $package3 = TourPackage::create([
            'name' => 'Chitwan Jungle Safari',
            'description' => 'Experience wildlife adventure in Chitwan National Park. Spot one-horned rhinos, Bengal tigers, and exotic birds. Enjoy elephant safari, jungle walks, and Tharu cultural dance.',
            'price' => 15000.00,
            'duration_days' => 3,
            'difficulty_level' => 'easy',
            'max_participants' => 12,
            'start_location_name' => 'Sauraha, Chitwan',
            'start_lat' => 27.5792,
            'start_lng' => 84.4956,
            'end_location_name' => 'Elephant Breeding Center',
            'end_lat' => 27.5234,
            'end_lng' => 84.4521,
            'is_active' => true,
        ]);

        $checkpoint3_1 = Checkpoint::create([
            'tour_package_id' => $package3->id,
            'name' => 'Rapti River Canoe Ride',
            'description' => 'Silent wildlife observation on traditional dugout canoe',
            'order' => 1,
            'latitude' => 27.5792,
            'longitude' => 84.4956,
            'radius' => 100,
            'estimated_time_from_previous' => 20,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint3_1->id,
            'title' => 'Crocodile Spotting',
            'content' => 'The Rapti River is home to two types of crocodiles: the fish-eating Gharial with its long thin snout, and the aggressive Marsh Mugger. Keep your hands inside the canoe!',
            'type' => 'safety',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint3_1->id,
            'title' => 'Bird Paradise',
            'content' => 'Over 500 bird species have been recorded in Chitwan. Watch for kingfishers, herons, egrets, and the rare Bengal Florican during your canoe ride.',
            'type' => 'info',
            'order' => 2,
        ]);

        $checkpoint3_2 = Checkpoint::create([
            'tour_package_id' => $package3->id,
            'name' => 'Jungle Safari',
            'description' => 'Elephant-back or jeep safari through dense jungle',
            'order' => 2,
            'latitude' => 27.5500,
            'longitude' => 84.4700,
            'radius' => 200,
            'estimated_time_from_previous' => 60,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint3_2->id,
            'title' => 'One-Horned Rhino',
            'content' => 'Chitwan is one of the last refuges of the endangered one-horned rhinoceros. The park has successfully increased rhino population from just 100 to over 600!',
            'type' => 'history',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint3_2->id,
            'title' => 'Bengal Tiger Territory',
            'content' => 'Though sightings are rare, Chitwan hosts around 120 Bengal tigers. Early morning safaris offer the best chance. Look for pugmarks (paw prints) and scratch marks on trees.',
            'type' => 'info',
            'order' => 2,
        ]);

        $checkpoint3_3 = Checkpoint::create([
            'tour_package_id' => $package3->id,
            'name' => 'Tharu Cultural Center',
            'description' => 'Experience indigenous Tharu culture and stick dance',
            'order' => 3,
            'latitude' => 27.5850,
            'longitude' => 84.5010,
            'radius' => 100,
            'estimated_time_from_previous' => 40,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint3_3->id,
            'title' => 'Indigenous Tharu People',
            'content' => 'The Tharu are the indigenous people of Chitwan, known for their unique culture and natural resistance to malaria. Their traditional mud houses keep cool in summer and warm in winter.',
            'type' => 'culture',
            'order' => 1,
        ]);

        CheckpointFact::create([
            'checkpoint_id' => $checkpoint3_3->id,
            'title' => 'Stick Dance Performance',
            'content' => 'The Tharu stick dance is a mesmerizing performance where dancers move in perfect sync, striking their sticks in rhythmic patterns. It\'s accompanied by traditional drums and songs.',
            'type' => 'culture',
            'order' => 2,
        ]);

        $this->command->info('Created 3 tour packages with checkpoints and facts');
        $this->command->info('Package 1: Kathmandu Valley Heritage Tour (3 checkpoints)');
        $this->command->info('Package 2: Pokhara Lakeside Adventure (3 checkpoints)');
        $this->command->info('Package 3: Chitwan Jungle Safari (3 checkpoints)');
        $this->command->info('Login credentials:');
        $this->command->info(' Admin: admin@paaila.com / password');
        $this->command->info(' Traveler: ram@example.com / password');
    }
}
