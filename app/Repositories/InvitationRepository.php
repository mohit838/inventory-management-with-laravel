<?php

namespace App\Repositories;

use App\Models\Invitation;

class InvitationRepository
{
    public function create(array $data): Invitation
    {
        return Invitation::create($data);
    }

    public function findByToken(string $token): ?Invitation
    {
        return Invitation::where('token', $token)->first();
    }

    public function markAsAccepted(Invitation $invitation): bool
    {
        return $invitation->update(['accepted_at' => now()]);
    }
}
