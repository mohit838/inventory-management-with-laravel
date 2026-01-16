<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Category permissions
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            // Product permissions
            'products.view', 'products.create', 'products.edit', 'products.delete',
            // Order permissions
            'orders.view', 'orders.create',
            // Dashboard permissions
            'dashboard.view', 'dashboard.view_revenue',
            // Settings & User Management
            'settings.manage',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 1. Super Admin - Has everything
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $superAdminRole->syncPermissions(Permission::all());

        // 2. Admin - Has everything except user management
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());
        $adminRole->revokePermissionTo('users.manage');

        // 3. User - View only + Create Orders
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'categories.view', 
            'products.view', 
            'orders.view', 
            'orders.create',
            'dashboard.view'
        ]);
    }
}
