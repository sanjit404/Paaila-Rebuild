<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Recommendation System...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->seedUsers();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('User seeded successfully!');
    }

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

}