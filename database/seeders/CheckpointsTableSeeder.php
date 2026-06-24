<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckpointsTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Look up packages by name
        $pkgs = DB::table('tour_packages')->get()->keyBy('name');

        $insert = [];

        // 1 Everest Panorama Trek checkpoints (real, well-known stops)
        if (isset($pkgs['Everest Panorama Trek'])) {
            $p = $pkgs['Everest Panorama Trek'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Lukla (Tenzing-Hillary Airport)',
                'description' => 'Small mountain airstrip; common start point for Everest region treks.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/4/45/Lukla_airport_%28Tenzing-Hillary%29.jpg',
                'latitude' => '27.68680000',
                'longitude' => '86.72940000',
                'order' => 1,
                'radius' => 150,
                'estimated_time_from_previous' => "30 min",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Phakding',
                'description' => 'First night stop downstream from Lukla, small riverside village.',
                'image' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.6480',
                'longitude' => '86.7036',
                'order' => 2,
                'radius' => 100,
                'estimated_time_from_previous' => "3 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Namche Bazaar',
                'description' => 'Gateway town to Everest region with shops, markets and acclimatisation options.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Namche_Bazaar.JPG',
                'latitude' => '27.8060',
                'longitude' => '86.7136',
                'order' => 3,
                'radius' => 120,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Tengboche Monastery',
                'description' => 'Famous monastery offering spectacular panoramic views of Everest and Ama Dablam.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/1/12/Tengboche_Monastery.JPG',
                'latitude' => '27.8378',
                'longitude' => '86.7167',
                'order' => 4,
                'radius' => 100,
                'estimated_time_from_previous' => "5 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 2 Annapurna Base Camp Trek checkpoints (real stops)
        if (isset($pkgs['Annapurna Base Camp Trek'])) {
            $p = $pkgs['Annapurna Base Camp Trek'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Naya Pul',
                'description' => 'Trailhead used by many ABC itineraries; road-accessible point.',
                'image' => 'https://images.unsplash.com/photo-1504198453319-5ce911bafcde?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.4580',
                'longitude' => '83.9206',
                'order' => 1,
                'radius' => 120,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Ghandruk',
                'description' => 'Traditional Gurung village with great mountain views and cultural experience.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/7/70/Ghandruk.JPG',
                'latitude' => '28.4650',
                'longitude' => '83.8890',
                'order' => 2,
                'radius' => 100,
                'estimated_time_from_previous' => "5 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Chomrong',
                'description' => 'Major village on the ABC route; junction for lower and upper trails.',
                'image' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.3849',
                'longitude' => '83.9632',
                'order' => 3,
                'radius' => 100,
                'estimated_time_from_previous' => "4 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Annapurna Base Camp (ABC)',
                'description' => 'The base camp itself, surrounded by the Annapurna massif; the trek\'s highlight.',
                'image' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.5429',
                'longitude' => '83.8203',
                'order' => 4,
                'radius' => 200,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 3 Langtang Valley Trek checkpoints
        if (isset($pkgs['Langtang Valley Trek'])) {
            $p = $pkgs['Langtang Valley Trek'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Syabrubesi',
                'description' => 'Trailhead village, local markets and starting point for Langtang trekkers.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/3/3b/Syabrubesi.jpg',
                'latitude' => '28.1033',
                'longitude' => '85.3147',
                'order' => 1,
                'radius' => 120,
                'estimated_time_from_previous' => "2 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Langtang Village',
                'description' => 'Village rebuilt after 2015; gateway to upper Langtang and local culture.',
                'image' => 'https://images.unsplash.com/photo-1505765058070-7b9b0f1e9d3b?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.2290',
                'longitude' => '85.4110',
                'order' => 2,
                'radius' => 100,
                'estimated_time_from_previous' => "5 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Kyanjin Gompa',
                'description' => 'Monastery and yak pasture area with superb mountain views.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/8/8b/Kyanjin_Gompa.jpg',
                'latitude' => '28.2250',
                'longitude' => '85.5390',
                'order' => 3,
                'radius' => 100,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Tserko Ri viewpoint',
                'description' => 'Climb near Kyanjin for panoramic views of Langtang Lirung and glaciers.',
                'image' => 'https://images.unsplash.com/photo-1504198453319-5ce911bafcde?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.2300',
                'longitude' => '85.5400',
                'order' => 4,
                'radius' => 100,
               'estimated_time_from_previous' => "4 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 4 Upper Mustang (real stops)
        if (isset($pkgs['Upper Mustang Trek'])) {
            $p = $pkgs['Upper Mustang Trek'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Jomsom',
                'description' => 'Regional hub and gateway town to Mustang via the Kali Gandaki valley.',
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.7929',
                'longitude' => '83.7383',
                'order' => 1,
                'radius' => 150,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Kagbeni',
                'description' => 'Ancient village and cultural gateway to Upper Mustang (cultures reminiscent of Tibet).',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/2/2f/Kagbeni.JPG',
                'latitude' => '28.8028',
                'longitude' => '83.7837',
                'order' => 2,
                'radius' => 100,
               'estimated_time_from_previous' => "4 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Chele',
                'description' => 'First major settlement entering Upper Mustang with scenic desert hills.',
                'image' => 'https://images.unsplash.com/photo-1549887534-9f3c1f3c8f7a?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.8750',
                'longitude' => '83.8400',
                'order' => 3,
                'radius' => 100,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Lo Manthang',
                'description' => 'Walled capital of the old Kingdom of Lo; cultural and historical center of Mustang.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/0/07/Lo_Manthang.jpg',
                'latitude' => '29.0011',
                'longitude' => '83.8659',
                'order' => 4,
                'radius' => 200,
                'estimated_time_from_previous' => "8 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 5 Muktinath pilgrimage
        if (isset($pkgs['Muktinath Pilgrimage Trek'])) {
            $p = $pkgs['Muktinath Pilgrimage Trek'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Jomsom',
                'description' => 'Gateway town for routes to Muktinath and Mustang.',
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.7929',
                'longitude' => '83.7383',
                'order' => 1,
                'radius' => 120,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Ghar Gompa',
                'description' => 'Small gompa on the approach to Muktinath, typical stop for trekkers.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Namche_Bazaar.JPG',
                'latitude' => '28.9300',
                'longitude' => '83.8500',
                'order' => 2,
                'radius' => 80,
               'estimated_time_from_previous' => "4 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Muktinath Temple',
                'description' => 'Sacred temple visited by both Hindus and Buddhists; pilgrimage destination.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/3/33/Muktinath_temple.jpg',
                'latitude' => '29.0306',
                'longitude' => '83.8964',
                'order' => 3,
                'radius' => 200,
                'estimated_time_from_previous' => "3 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Charang',
                'description' => 'Traditional village often included on routes to Muktinath; cultural stop.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/7/7a/Charang_Mustang.jpg',
                'latitude' => '29.0360',
                'longitude' => '83.9000',
                'order' => 4,
                'radius' => 100,
               'estimated_time_from_previous' => "4 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 6 Chitwan Wildlife Safari checkpoints
        if (isset($pkgs['Chitwan Wildlife Safari'])) {
            $p = $pkgs['Chitwan Wildlife Safari'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Sauraha',
                'description' => 'Tourist village on the edge of Chitwan National Park; safari start point.',
                'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.5167',
                'longitude' => '84.4500',
                'order' => 1,
                'radius' => 150,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Rapti River Bank',
                'description' => 'River bank area for boat safaris and birdwatching inside the park.',
                'image' => 'https://images.unsplash.com/photo-1504198453319-5ce911bafcde?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.5333',
                'longitude' => '84.4333',
                'order' => 2,
                'radius' => 200,
                'estimated_time_from_previous' => "2 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Elephant Corridor / Safari Zone',
                'description' => 'Area used for jeep/elephant safaris; chance to spot rhinos and deer.',
                'image' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.5160',
                'longitude' => '84.4600',
                'order' => 3,
                'radius' => 300,
                'estimated_time_from_previous' => "3 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Tharu Village Visit',
                'description' => 'Visit to local Tharu community to learn about traditional culture and dance.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/5/5b/Tharu_dance.jpg',
                'latitude' => '27.5200',
                'longitude' => '84.4550',
                'order' => 4,
                'radius' => 100,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 7 Tsum Valley Trek checkpoints
        if (isset($pkgs['Tsum Valley Trek'])) {
            $p = $pkgs['Tsum Valley Trek'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Soti Khola',
                'description' => 'Trailhead village; common entry point for Tsum Valley routes.',
                'image' => 'https://images.unsplash.com/photo-1505765058070-7b9b0f1e9d3b?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.2580',
                'longitude' => '84.1520',
                'order' => 1,
                'radius' => 120,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Chumling',
                'description' => 'Small village offering insight into local life in Tsum Valley.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/7/70/Ghandruk.JPG',
                'latitude' => '28.6530',
                'longitude' => '84.2800',
                'order' => 2,
                'radius' => 80,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Mu Gompa',
                'description' => 'High gompa and meditation site visited by pilgrims and trekkers.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/3/33/Muktinath_temple.jpg',
                'latitude' => '28.6800',
                'longitude' => '84.3000',
                'order' => 3,
                'radius' => 100,
                'estimated_time_from_previous' => "7 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Chokhopani (local stop)',
                'description' => 'Mountain viewpoint area used as a rest/observation stop.',
                'image' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.7000',
                'longitude' => '84.3200',
                'order' => 4,
                'radius' => 100,
               'estimated_time_from_previous' => "4 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 8 Gokyo Lakes & Historical Route checkpoints
        if (isset($pkgs['Gokyo Lakes & Historical Route'])) {
            $p = $pkgs['Gokyo Lakes & Historical Route'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Phakding',
                'description' => 'Village en route from Lukla; common overnight stop for Gokyo trek.',
                'image' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.6480',
                'longitude' => '86.7036',
                'order' => 1,
                'radius' => 100,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Namche Bazaar',
                'description' => 'Main hub for the region, supplies and acclimatisation opportunities.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/6/6a/Namche_Bazaar.JPG',
                'latitude' => '27.8060',
                'longitude' => '86.7136',
                'order' => 2,
                'radius' => 120,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Dole',
                'description' => 'Small settlement on the route to Gokyo with tea-houses and views.',
                'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.8670',
                'longitude' => '86.7000',
                'order' => 3,
                'radius' => 80,
                'estimated_time_from_previous' => "5 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Gokyo Lakes',
                'description' => 'Famous glacial lakes offering spectacular reflections of surrounding peaks.',
                'image' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.9546',
                'longitude' => '86.6971',
                'order' => 4,
                'radius' => 200,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 9 Nagarkot Sunrise Walk checkpoints
        if (isset($pkgs['Nagarkot Sunrise Walk'])) {
            $p = $pkgs['Nagarkot Sunrise Walk'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Nagarkot Viewpoint',
                'description' => 'Popular sunrise viewpoint with panoramic Himalayan vista.',
                'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.7061',
                'longitude' => '85.5118',
                'order' => 1,
                'radius' => 200,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Shivapuri Hillstop',
                'description' => 'Short trail stop with local views and small temples.',
                'image' => 'https://images.unsplash.com/photo-1504198453319-5ce911bafcde?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.7040',
                'longitude' => '85.5100',
                'order' => 2,
                'radius' => 80,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Local Village Trail',
                'description' => 'Walk through nearby villages to experience rural life and local snacks.',
                'image' => 'https://images.unsplash.com/photo-1505765058070-7b9b0f1e9d3b?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.7050',
                'longitude' => '85.5120',
                'order' => 3,
                'radius' => 60,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Sunrise Photo Spot',
                'description' => 'Designated spot for sunrise photography with unobstructed views.',
                'image' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '27.7070',
                'longitude' => '85.5130',
                'order' => 4,
                'radius' => 50,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 10 Karnali Adventure Combo checkpoints
        if (isset($pkgs['Karnali Adventure Combo'])) {
            $p = $pkgs['Karnali Adventure Combo'];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Surkhet',
                'description' => 'Regional town serving as logistic hub for Karnali adventures.',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.6167',
                'longitude' => '81.6167',
                'order' => 1,
                'radius' => 150,
                'estimated_time_from_previous' => "1 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Dang Valley Approach',
                'description' => 'Start of trekking approaches with river views and rural landscapes.',
                'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.7000',
                'longitude' => '81.7000',
                'order' => 2,
                'radius' => 120,
               'estimated_time_from_previous' => "4 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Karnali River Rapids',
                'description' => 'River section used for rafting and water activities in the package.',
                'image' => 'https://images.unsplash.com/photo-1504198453319-5ce911bafcde?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.7500',
                'longitude' => '81.7500',
                'order' => 3,
                'radius' => 300,
                'estimated_time_from_previous' => "2 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $insert[] = [
                'tour_package_id' => $p->id,
                'name' => 'Remote Trek Camp',
                'description' => 'Typical overnight camp location offering wilderness experience.',
                'image' => 'https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=1200&auto=format&fit=crop',
                'latitude' => '28.7700',
                'longitude' => '81.7600',
                'order' => 4,
                'radius' => 150,
                'estimated_time_from_previous' => "6 hr",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // finally insert if we have items
        if (!empty($insert)) {
            DB::table('checkpoints')->insert($insert);
        }
    }
}