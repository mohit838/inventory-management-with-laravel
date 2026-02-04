<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;

class RolePermissionController extends Controller
{
    /**
     * Display a matrix of roles and permissions.
     */
    public function index()
    {
        Gate::authorize('manage_permissions');

        // Priority Hierarchy
        $priorityOrder = ['superadmin' => 1, 'admin' => 2, 'owner' => 3, 'employee' => 4];

        $roles = Role::all()->sortBy(function($role) use ($priorityOrder) {
            return $priorityOrder[$role->name] ?? 99;
        })->values();

        // We still skip superadmin for dynamic editing in the matrix as they should always have all
        $editableRoles = $roles->filter(fn($r) => $r->name !== 'superadmin');

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

    /**
     * Update the permissions for specific roles.
     */
    public function update(Request $request)
    {
        Gate::authorize('manage_permissions');

        $request->validate([
            'permissions' => 'array',
        ]);

        $roles = Role::where('name', '!=', 'superadmin')->get();

        foreach ($roles as $role) {
            $rolePermissions = $request->permissions[$role->id] ?? [];
            $role->syncPermissions($rolePermissions);
        }

        return back()->with('success', 'Security matrix updated successfully.');
    }
}
