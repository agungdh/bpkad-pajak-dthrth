<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Create factory users and assign pegawai role
        User::factory(10)->create()->each(function ($user) {
            $user->assignRole('pegawai');
        });

        // Create test user and assign admin role
        $testUser = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Test User',
                'username' => 'admin',
                'email' => 'test@example.com',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $testUser->assignRole('admin');
    }
}
