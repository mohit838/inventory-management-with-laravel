<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        // Super Admin
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name' => 'Super Admin User',
                'password' => bcrypt('password'),
                'role' => User::ROLE_SUPERADMIN,
            ]
        );


        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );


        // Regular User
        $user = User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'role' => User::ROLE_USER,
            ]
        );

    }
}
