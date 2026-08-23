<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Invitation;
use Illuminate\Notifications\Notification;

/* Notification when a user is invited to a workspace or a team within a new workspace. */
class InvitationReceivedNotification extends Notification
{
    public function __construct(protected Invitation $invitation) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function type(): NotificationType
    {
        return NotificationType::InvitationReceived;
    }

    /**
     * @return array{type: string, title: string, body: string, url: null, avatar_url: string|null, avatar_initials: string, invitation_id: int}
     */
    public function toDatabase(object $notifiable): array
    {
        $target = $this->invitation->target();

        $body = $this->invitation->team
            ? "{$this->invitation->invitedBy->name} invited you to join {$this->invitation->team->name} in {$this->invitation->workspace->name}."
            : "{$this->invitation->invitedBy->name} invited you to join {$this->invitation->workspace->name}.";

        return [
            'type' => $this->type()->value,
            'title' => $target->name,
            'body' => $body,
            'url' => null,
            'avatar_url' => $target->logo_url,
            'avatar_initials' => $target->initials(),
            'invitation_id' => $this->invitation->id,
        ];
    }
}
