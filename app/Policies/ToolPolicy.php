<?php

namespace App\Policies;

use App\Enums\ToolVisibility;
use App\Models\Tool;
use App\Models\User;

class ToolPolicy
{
    /**
     * Determine whether the user can view this tool: Public tools are open to everyone,
     * Private tools only to the owning team's members, Unlisted tools to the owning team's
     * members or to anyone presenting a matching share token.
     */
    public function view(User $user, Tool $tool, ?string $token = null): bool
    {
        return match ($tool->visibility) {
            ToolVisibility::Public => true,
            ToolVisibility::Private => $tool->team->hasMember($user),
            ToolVisibility::Unlisted => $tool->team->hasMember($user)
                || ($token !== null && $tool->share_token !== null && hash_equals($tool->share_token, $token)),
        };
    }
}
