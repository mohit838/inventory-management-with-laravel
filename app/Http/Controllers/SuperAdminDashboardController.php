<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use App\Constants\AppConstant;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // Global User Health
        $stats = [
            'total_users'    => User::withTrashed()->count(),
            'active_users'   => User::count(),
            'inactive_users' => User::onlyTrashed()->count(),
        ];

        // Role Distribution
        $roles = Role::all()->mapWithKeys(function ($role) {
            return [$role->name => User::role($role->name)->count()];
        });

        // Tenant Overview - Paginating for scale
        $tenants = Tenant::withCount(['users'])->paginate(AppConstant::DEFAULT_PAGINATION)->through(function($tenant) {
            return [
                'name'           => $tenant->name,
                'total_members'  => $tenant->users_count,
                'owners'         => User::role(AppConstant::ROLE_OWNER)->where('tenant_id', $tenant->id)->count(),
                'employees'      => User::role(AppConstant::ROLE_EMPLOYEE)->where('tenant_id', $tenant->id)->count(),
            ];
        });

        return view('superadmin.dashboard', compact('stats', 'roles', 'tenants'));
    }
}
