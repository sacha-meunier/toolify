<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Determine whether the user can manage this team's general settings.
     */
    public function manageTeam(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    /**
     * Determine whether the user can manage this team's members.
     */
    public function manageMembers(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    /**
     * Determine whether the user can invite members to this team, and manage its pending
     * invitations.
     */
    public function inviteMembers(User $user, Team $team): bool
    {
        return $team->workspace->owner_id === $user->id;
    }

    /**
     * Determine whether the user can manage this team's stack.
     */
    public function manageStack(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    /**
     * Determine whether the user can manage this team's surveys.
     */
    public function manageSurveys(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }

    /**
     * Determine whether the user can manage this team's public listing.
     */
    public function manageListing(User $user, Team $team): bool
    {
        return $team->hasMember($user);
    }
}
