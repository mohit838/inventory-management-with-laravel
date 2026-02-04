<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Constants\AppConstant;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    public function index(Request $request)
    {
        Gate::authorize(AppConstant::PERM_VIEW_USERS);
        
        $users = $this->userService->getPaginatedUsers();
        return view('users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        Gate::authorize(AppConstant::PERM_DELETE_USERS);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $this->userService->deleteUser($user);
        return back()->with('success', 'User deactivated successfully.');
    }

    public function restore($id)
    {
        Gate::authorize(AppConstant::PERM_VIEW_USERS);
        
        $this->userService->restoreUser($id);
        return back()->with('success', 'User activated successfully.');
    }
}
