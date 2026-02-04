<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class SuperAdminDashboardController extends Controller
{
    /**
     * Display the system-wide infrastructure dashboard.
     */
    public function index()
    {
        // Strictly restricted to Superadmin via controller check
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized access to Infrastructure Dashboard.');
        }

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

        // Tenant Overview (Hierarchical Insights) - Paginating for scale
        $tenants = Tenant::withCount(['users'])->paginate(10)->through(function($tenant) {
            return [
                'name'           => $tenant->name,
                'total_members'  => $tenant->users_count,
                'owners'         => User::role('owner')->where('tenant_id', $tenant->id)->count(),
                'employees'      => User::role('employee')->where('tenant_id', $tenant->id)->count(),
                'status'         => 'Healthy', // Mock status
                'mock_revenue'   => number_format(rand(5000, 50000), 2), // Mock revenue
            ];
        });

        return view('superadmin.dashboard', compact('stats', 'roles', 'tenants'));
    }
}
