<?php

namespace App\Services;

use App\Models\User;
use App\Constants\AppConstant;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Get paginated users for the current tenant.
     */
    public function getPaginatedUsers(int $perPage = AppConstant::DEFAULT_PAGINATION): LengthAwarePaginator
    {
        return User::withTrashed()
            ->with(['roles', 'tenant'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Soft delete a user.
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restoreUser(int $userId): bool
    {
        $user = User::onlyTrashed()->findOrFail($userId);
        return $user->restore();
    }
}
