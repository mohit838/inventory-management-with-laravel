<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $newUser)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->newUser->id,
            'name' => $this->newUser->name,
            'email' => $this->newUser->email,
            'role' => $this->newUser->roles->first()->name ?? 'Member',
            'message' => "New " . ($this->newUser->roles->first()->name ?? 'Member') . " created: " . $this->newUser->name,
        ];
    }
}
