<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function __construct(protected InvitationService $invitationService)
    {
    }

    public function create()
    {
        return view('invitations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,owner,employee',
        ]);

        // Authorization check - only those with permission can invite
        if (!Auth::user()->can('invite_users')) {
            abort(403, 'Unauthorized action.');
        }

        // Determine tenant_id
        $tenantId = Auth::user()->tenant_id;
        
        // Superadmins/Admins might invite Owners without a tenant context initially
        if (Auth::user()->hasRole(['superadmin', 'admin']) && $request->role === 'owner') {
            $tenantId = null;
        }

        $invitation = $this->invitationService->generateInvitation(
            $request->email,
            $request->role,
            $tenantId
        );

        Mail::to($request->email)->send(new InvitationMail($invitation));

        return back()->with('success', 'Invitation sent successfully to ' . $request->email);
    }
}
