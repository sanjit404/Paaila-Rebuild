<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\TourPackage;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // ── Guard: skip entirely if posts already exist ───────────────
        // This means running `db:seed` or `db:seed --class=PostSeeder`
        // a second time does nothing, which is safe for production.
        if (Post::exists()) {
            $this->command->info('⏭  PostSeeder: posts already exist — skipping.');
            return;
        }

        $packages = TourPackage::all();

        if ($packages->isEmpty()) {
            $this->command->warn('⚠  PostSeeder: no trek packages found. Run TourAppSeeder first.');
            return;
        }

        // ── Helper: safely get package by index ───────────────────────
        $pkg = fn(int $index) => $packages->values()->get($index)?->id
                               ?? $packages->first()->id;

        $posts = [
            // ── OFFERS ────────────────────────────────────────────────
            [
                'title'          => 'Limited Time: 30% Off All Nepal Treks!',
                'content'        => "Book any trek package this month and save 30%! This exclusive offer includes full GPS tracking, expert guides, and all safety equipment. Don't miss this incredible opportunity to explore the Himalayas at an unbeatable price. Offer valid until end of month. Book now and start your adventure!",
                'type'           => 'offer',
                'image'          => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200',
                'is_highlighted' => true,
                'trek_id'        => $pkg(0),
                'likes_count'    => 45,
            ],
            [
                'title'          => 'Early Bird Special: Save 20% on Summer Treks',
                'content'        => "Book your summer trek now and enjoy 20% off! Perfect weather, clear mountain views, and amazing trails await. Limited slots available. Early bird offer ends soon. Includes all safety features and GPS tracking.",
                'type'           => 'offer',
                'image'          => 'https://images.unsplash.com/photo-1483728642387-6c3bdd6c93e5?w=800',
                'is_highlighted' => false,
                'trek_id'        => null,
                'likes_count'    => 56,
            ],
            [
                'title'          => 'Group Discount: Bring 5+ Friends, Get 25% Off',
                'content'        => "Planning a group trek? Bring 5 or more friends and enjoy 25% off the total booking. Perfect for corporate teams, friend groups, or family adventures. Contact us to customize your group trek package.",
                'type'           => 'offer',
                'image'          => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800',
                'is_highlighted' => false,
                'trek_id'        => null,
                'likes_count'    => 41,
            ],

            // ── TREK POSTS ─────────────────────────────────────────────
            [
                'title'          => 'Discover Kathmandu Valley Heritage Sites',
                'content'        => "Explore ancient temples, vibrant markets, and UNESCO World Heritage sites in the heart of Nepal. Our expert guides will take you through centuries of history and culture. Perfect for first-time visitors and culture enthusiasts. Includes GPS tracking for your safety.",
                'type'           => 'trek',
                'image'          => 'https://images.unsplash.com/photo-1605640840605-14ac1855827b?w=800',
                'is_highlighted' => false,
                'trek_id'        => $pkg(0),
                'likes_count'    => 28,
            ],
            [
                'title'          => 'Pokhara Adventure: Lakes & Mountains',
                'content'        => "Experience the stunning beauty of Pokhara with visits to Phewa Lake, Peace Pagoda, and sunrise at Sarangkot. This 3-day adventure combines natural beauty with cultural insights. Suitable for families and solo travelers. Real-time GPS tracking included.",
                'type'           => 'trek',
                'image'          => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=800',
                'is_highlighted' => false,
                'trek_id'        => $pkg(1),
                'likes_count'    => 34,
            ],

            // ── NEWS ──────────────────────────────────────────────────
            [
                'title'          => 'New GPS Tracking Feature: Track Your Loved Ones in Real-Time',
                'content'        => "We're excited to announce our enhanced GPS tracking system! Now family and friends can monitor your trek progress in real-time with a secure PIN. Features include checkpoint notifications, live location updates every 5 seconds, and safety alerts. Your safety is our priority.",
                'type'           => 'news',
                'image'          => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?w=800',
                'is_highlighted' => false,
                'trek_id'        => null,
                'likes_count'    => 67,
            ],
            [
                'title'          => 'Safety First: Our New Emergency Response System',
                'content'        => "Introducing our 24/7 emergency response system for all treks. Trained professionals monitor active treks and can dispatch help within minutes if needed. Combined with GPS tracking and regular check-ins, we ensure maximum safety for all our trekkers.",
                'type'           => 'news',
                'image'          => 'https://images.unsplash.com/photo-1584467735815-f778f274e296?w=800',
                'is_highlighted' => false,
                'trek_id'        => null,
                'likes_count'    => 53,
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($posts as $postData) {
            /*
             * firstOrCreate(uniqueKey, otherColumns)
             *
             * - Searches by 'title' only (the unique business key).
             * - If found  → returns existing record, does NOT update it.
             * - If missing → creates a fresh record with all columns.
             *
             * This means the seeder is safe to re-run after the early-exit
             * guard above is removed (e.g. during development).
             */
            [$post, $wasCreated] = [
                Post::firstOrCreate(
                    ['title' => $postData['title']],   // search key
                    $postData                           // fill on create
                ),
                false,
            ];

            // firstOrCreate doesn't return a boolean, so we check wasRecentlyCreated
            if ($post->wasRecentlyCreated) {
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->command->info("✅  PostSeeder: {$created} created, {$skipped} already existed.");
    }
}