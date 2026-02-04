<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function __construct(protected \App\Services\InvitationService $invitationService)
    {
    }

    public function create()
    {
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,owner,employee',
        ]);

        \Illuminate\Support\Facades\Gate::authorize('invite_users');

        $tenantId = Auth::user()->tenant_id;
        
        // Superadmins/Admins might invite Owners without a tenant context initially
        if (Auth::user()->hasRole(['superadmin', 'admin']) && $request->role === 'owner') {
            $tenantId = null;
        }

        $this->invitationService->sendInvitation(
            $request->email,
            $request->role,
            $tenantId
        );

        return back()->with('success', 'Invitation sent successfully to ' . $request->email);
    }
}
