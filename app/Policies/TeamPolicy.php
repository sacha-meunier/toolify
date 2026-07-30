<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
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
}
