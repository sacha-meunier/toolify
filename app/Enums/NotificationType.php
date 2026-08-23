<?php

namespace App\Enums;

enum NotificationType: string
{
    case TeamMemberAdded = 'team_member_added';
    case InvitationReceived = 'invitation_received';

    /**
     * The icon shown next to notifications of this type in the inbox.
     */
    public function icon(): string
    {
        return match ($this) {
            self::TeamMemberAdded => 'user',
            self::InvitationReceived => 'user-group',
        };
    }
}
