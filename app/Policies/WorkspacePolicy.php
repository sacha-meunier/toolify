<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
    /**
     * Determine whether the user can manage this workspace's general settings.
     */
    public function manageWorkspace(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }

    /**
     * Determine whether the user can manage this workspace's members.
     */
    public function manageMembers(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }

    /**
     * Determine whether the user can manage this workspace's stack.
     */
    public function manageStack(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }

    /**
     * Determine whether the user can manage this workspace's surveys.
     */
    public function manageSurveys(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }

    /**
     * Determine whether the user can create a team within this workspace.
     */
    public function createTeam(User $user, Workspace $workspace): bool
    {
        return $workspace->hasMember($user);
    }
}
