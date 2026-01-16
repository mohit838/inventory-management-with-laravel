<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Categories
            ['name' => 'View Categories', 'slug' => 'categories.view', 'group' => 'categories'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'group' => 'categories'],
            ['name' => 'Edit Categories', 'slug' => 'categories.edit', 'group' => 'categories'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'group' => 'categories'],

            // Products
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'products'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'group' => 'products'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'group' => 'products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'group' => 'products'],

            // Settings
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'settings'],
        ];

        foreach ($permissions as $perm) {
            \App\Models\Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }
    }
}
