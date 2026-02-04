<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;

class InvitationService
{
    /**
     * Generate an invitation and send the mail.
     */
    public function sendInvitation(string $email, string $role, ?int $tenantId): Invitation
    {
        $invitation = Invitation::create([
            'email' => $email,
            'role' => $role,
            'tenant_id' => $tenantId,
            'token' => Str::random(32),
        ]);

        Mail::to($email)->send(new InvitationMail($invitation));

        return $invitation;
    }
}
