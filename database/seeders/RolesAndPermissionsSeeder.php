<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Constants\AppConstant;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define Module Config (Matching AppConstant Expectations)
        $modules = [
            'dashboard'      => ['view'],
            'users'          => ['view', 'create', 'edit', 'delete'],
            'invitations'    => ['view', 'create'],
            'settings'       => ['view', 'manage'],
            'permissions'    => ['manage'],
            'diagnostics'    => ['view'],
            'infrastructure' => ['view'],
            'inventory'      => ['view', 'create', 'edit', 'delete'],
        ];

        // 3. Create Permissions
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action}_{$module}");
            }
        }

        // 4. Role Mapping
        $roles = [
            AppConstant::ROLE_SUPERADMIN => Permission::all()->pluck('name')->toArray(),
            
            AppConstant::ROLE_ADMIN      => [
                AppConstant::PERM_VIEW_DASHBOARD, AppConstant::PERM_VIEW_USERS, 
                'view_invitations', AppConstant::PERM_CREATE_INVITATIONS,
                'view_inventory', 'create_inventory', 'edit_inventory', AppConstant::PERM_VIEW_SETTINGS,
            ],

            AppConstant::ROLE_OWNER      => [
                AppConstant::PERM_VIEW_DASHBOARD, AppConstant::PERM_VIEW_USERS, 'create_users', 'edit_users', AppConstant::PERM_DELETE_USERS,
                'view_invitations', AppConstant::PERM_CREATE_INVITATIONS, 'view_inventory', 'create_inventory',
                'edit_inventory', 'delete_inventory', AppConstant::PERM_VIEW_SETTINGS, 'manage_settings',
            ],

            AppConstant::ROLE_EMPLOYEE   => [
                AppConstant::PERM_VIEW_DASHBOARD, AppConstant::PERM_VIEW_SETTINGS, 
                'view_inventory', 'create_inventory', 'edit_inventory',
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
