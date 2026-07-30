<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;

class SurveyPolicy
{
    /**
     * Determine whether the user can view this survey.
     */
    public function view(User $user, Survey $survey): bool
    {
        return $this->owns($user, $survey);
    }

    /**
     * Determine whether the user can update this survey.
     */
    public function update(User $user, Survey $survey): bool
    {
        return $this->owns($user, $survey);
    }

    /**
     * Determine whether the user can delete this survey.
     */
    public function delete(User $user, Survey $survey): bool
    {
        return $this->owns($user, $survey);
    }

    /**
     * Determine whether the given user has access to this survey's owner (their own personal
     * scope, or a workspace/team they belong to).
     */
    private function owns(User $user, Survey $survey): bool
    {
        return match (true) {
            $survey->owner instanceof User => $survey->owner_id === $user->id,
            $survey->owner instanceof Workspace, $survey->owner instanceof Team => $survey->owner->hasMember($user),
            default => false,
        };
    }
}
