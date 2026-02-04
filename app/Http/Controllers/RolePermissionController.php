<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use App\Constants\AppConstant;

class RolePermissionController extends Controller
{
    public function index()
    {
        Gate::authorize(AppConstant::PERM_MANAGE_PERMISSIONS);

        // Priority Hierarchy using Constants
        $priorityOrder = [
            AppConstant::ROLE_SUPERADMIN => 1, 
            AppConstant::ROLE_ADMIN => 2, 
            AppConstant::ROLE_OWNER => 3, 
            AppConstant::ROLE_EMPLOYEE => 4
        ];

        $roles = Role::all()->sortBy(function($role) use ($priorityOrder) {
            return $priorityOrder[$role->name] ?? 99;
        })->values();

        // We still skip superadmin for dynamic editing
        $editableRoles = $roles->filter(fn($r) => $r->name !== AppConstant::ROLE_SUPERADMIN);

        $permissions = Permission::all();

        // Group permissions by module
        $groupedPermissions = $permissions->groupBy(function($item) {
            $parts = explode('_', $item->name);
            return count($parts) > 1 ? str_replace('_', ' ', end($parts)) : 'System';
        });

        return view('settings.permissions', [
            'roles' => $editableRoles,
            'groupedPermissions' => $groupedPermissions
        ]);
    }

    public function update(Request $request)
    {
        Gate::authorize(AppConstant::PERM_MANAGE_PERMISSIONS);

        $request->validate(['permissions' => 'array']);

        $roles = Role::where('name', '!=', AppConstant::ROLE_SUPERADMIN)->get();

        foreach ($roles as $role) {
            $rolePermissions = $request->permissions[$role->id] ?? [];
            $role->syncPermissions($rolePermissions);
        }

        return back()->with('success', 'Security matrix updated successfully.');
    }
}
