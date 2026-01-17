<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\User;

trait HasPermissions
{
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    /**
     * Check if user has specific permission.
     * Logic:
     * - Superadmin: All access.
     * - Admin/Employee: Must have explicit permission.
     *   (Admin usually has permissions assigned during subscription/setup).
     */
    public function hasPermissionTo(string $permissionSlug): bool
    {
        // 1. Superadmin Bypass
        if ($this->role === User::ROLE_SUPERADMIN) {
            return true;
        }

        // 2. Check Database Permissions (Cached ideally, but eager loading works too)
        // We'll check if the permission exists in the relation.
        return $this->permissions->contains('slug', $permissionSlug);
    }

    /**
     * Assign permissions to a user.
     */
    public function givePermissionTo(array|string $permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $ids = Permission::whereIn('slug', $permissions)->pluck('id');
        $this->permissions()->syncWithoutDetaching($ids);
    }

    public function revokePermissionTo(array|string $permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $ids = Permission::whereIn('slug', $permissions)->pluck('id');
        $this->permissions()->detach($ids);
    }
}
