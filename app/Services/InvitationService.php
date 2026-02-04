<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\InvitationRepository;
use App\Repositories\TenantRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InvitationService
{
    public function __construct(
        protected InvitationRepository $invitationRepository,
        protected TenantRepository $tenantRepository
    ) {}

    public function generateInvitation(string $email, string $role, ?int $tenantId = null): Invitation
    {
        return $this->invitationRepository->create([
            'email'      => $email,
            'role'       => $role,
            'tenant_id'  => $tenantId,
            'token'      => Str::random(40),
            'expires_at' => now()->addHours(24),
        ]);
    }

    public function validateToken(string $token): ?Invitation
    {
        $invitation = $this->invitationRepository->findByToken($token);

        if (!$invitation || $invitation->accepted_at || $invitation->isExpired()) {
            return null;
        }

        return $invitation;
    }

    public function acceptInvitation(Invitation $invitation, array $userData): User
    {
        return DB::transaction(function () use ($invitation, $userData) {
            $tenantId = $invitation->tenant_id;

            // If the role is 'owner' and invited by Superadmin, we might need to create a tenant
            if ($invitation->role === 'owner' && !$tenantId) {
                $tenant = $this->tenantRepository->create([
                    'name' => $userData['tenant_name'] ?? ($userData['name'] . "'s Organization"),
                    'slug' => Str::slug($userData['tenant_name'] ?? $userData['name']),
                ]);
                $tenantId = $tenant->id;
            }

            $user = User::create([
                'name'      => $userData['name'],
                'email'     => $invitation->email,
                'password'  => Hash::make($userData['password']),
                'tenant_id' => $tenantId,
            ]);

            $user->assignRole($invitation->role);

            $this->invitationRepository->markAsAccepted($invitation);

            // Notify Superadmins and Admins
            $admins = User::role(['superadmin', 'admin'])->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\UserCreatedNotification($user));

            return $user;
        });
    }
}
