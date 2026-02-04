<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define Module Config (Matching Dynamic Menu Expectations)
        $modules = [
            'dashboard'   => ['view'],
            'users'       => ['view', 'create', 'edit', 'delete'],
            'invitations' => ['view', 'create'],
            'settings'    => ['view', 'manage'],
            'permissions' => ['manage'],
            'diagnostics' => ['view'],
            'infrastructure' => ['view'],
            'inventory'   => ['view', 'create', 'edit', 'delete'],
        ];

        // 3. Create Permissions
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action}_{$module}");
            }
        }

        // 4. Role Mapping
        $roles = [
            'superadmin' => Permission::all()->pluck('name')->toArray(),
            
            'admin'      => [
                'view_dashboard', 'view_users', 'view_invitations', 'create_invitations',
                'view_inventory', 'create_inventory', 'edit_inventory', 'view_settings',
            ],

            'owner'      => [
                'view_dashboard', 'view_users', 'create_users', 'edit_users', 'delete_users',
                'view_invitations', 'create_invitations', 'view_inventory', 'create_inventory',
                'edit_inventory', 'delete_inventory', 'view_settings', 'manage_settings',
            ],

            'employee'   => [
                'view_dashboard', 'view_inventory', 'create_inventory', 'edit_inventory',
            ],
        ];

        // 5. Sync
        foreach ($roles as $name => $perms) {
            Role::findOrCreate($name)->syncPermissions($perms);
        }

        // 6. Bootstrap Superadmin
        $superAdmin = User::firstOrCreate(['email' => 'superadmin@example.com'], [
            'name'     => 'Super Admin',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('superadmin');
    }
}
