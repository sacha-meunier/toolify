<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Notifications\Notification;

/* Notification when a user is invited in a team within the same workspace. */
class TeamMemberAddedNotification extends Notification
{
    public function __construct(protected Team $team, protected User $addedBy) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function type(): NotificationType
    {
        return NotificationType::TeamMemberAdded;
    }

    /**
     * @return array{type: string, title: string, body: string, url: null, avatar_url: string|null, avatar_initials: string}
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type()->value,
            'title' => $this->team->name,
            'body' => "{$this->addedBy->name} added you to {$this->team->name}. Feel free to check out the stack and surveys to get started.",
            'url' => null,
            'avatar_url' => $this->team->logo_url,
            'avatar_initials' => $this->team->initials(),
        ];
    }
}
