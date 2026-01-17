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
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        $modules = [
            'categories' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete'],
            'orders' => ['view', 'create'],
            'dashboard' => ['view', 'view_revenue'],
            'settings' => ['manage'],
            'users' => ['manage'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $slug = "$module.$action";
                \App\Models\Permission::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => ucfirst($action) . ' ' . ucfirst($module),
                        'group' => $module,
                        'guard_name' => 'web'
                    ]
                );
            }
        }

        // We still create roles in Spatie's table for compatibility if needed, 
        // but our primary check is now via HasPermissions trait and slugs.
    }
}
