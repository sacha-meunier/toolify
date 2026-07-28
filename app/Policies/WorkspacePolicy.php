<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine whether the user can manage this workspace's stack.
     */
    public function manageStack(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }
}
