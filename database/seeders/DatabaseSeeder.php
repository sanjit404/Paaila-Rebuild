<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TourAppSeeder::class,  // Existing seeder (users, packages, checkpoints)
            PostSeeder::class,      // NEW: Feed content
        ]);
    }
}