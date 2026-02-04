<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        Auth::user()->can('view_users') ?: abort(403);

        $query = User::with(['roles', 'tenant'])->withTrashed();

        // Single-database multi-tenancy scoping
        // Users can only see their own tenant unless they have global view permissions
        if (!Auth::user()->hasRole(['superadmin', 'admin'])) {
            $query->where('tenant_id', Auth::user()->tenant_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Soft delete a user.
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        // 1. Granular Permission Check
        if (!$currentUser->can('delete_users')) {
            return back()->with('error', 'Insufficient permissions to deactivate users.');
        }

        // 2. Prevent self-deletion
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        // 3. Hierarchy Logic
        $canDelete = false;

        if ($currentUser->hasRole('superadmin')) {
            $canDelete = true;
        } 
        elseif ($currentUser->hasRole('admin')) {
            // Admin can delete Owners and Employees
            if ($user->hasRole(['owner', 'employee'])) {
                $canDelete = true;
            }
        } 
        elseif ($currentUser->hasRole('owner')) {
            // Owner can only delete Employees in their own tenant
            if ($user->hasRole('employee') && $user->tenant_id === $currentUser->tenant_id) {
                $canDelete = true;
            }
        }

        if (!$canDelete) {
            return back()->with('error', 'Hierarchy restriction: You cannot deactivate this specific user.');
        }

        $user->delete();

        return back()->with('success', 'User deactivated successfully.');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        $currentUser = Auth::user();
        $user = User::withTrashed()->findOrFail($id);
        
        // 1. Granular Permission Check (Re-using edit or create depending on policy, but let's use edit_users)
        if (!$currentUser->can('edit_users')) {
            return back()->with('error', 'Insufficient permissions to activate users.');
        }

        // Logic check for Hierarchy
        if ($currentUser->hasRole('owner') && $user->tenant_id !== $currentUser->tenant_id) {
            abort(403);
        }

        $user->restore();

        return back()->with('success', 'User activated successfully.');
    }
}
