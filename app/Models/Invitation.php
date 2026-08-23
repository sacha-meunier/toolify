<?php

namespace App\Models;

use App\Enums\InvitationStatus;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * An invitation to join a workspace, or a specific team within one. `team_id` is null for a
 * workspace-level invitation ; when set, accepting also implies joining the parent workspace.
 *
 * @property int $id
 * @property int $workspace_id
 * @property int|null $team_id
 * @property int $invited_by_id
 * @property string $email
 * @property InvitationStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['workspace_id', 'team_id', 'invited_by_id', 'email', 'status'])]
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => InvitationStatus::class,
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    /**
     * The team being joined, or the workspace itself for a workspace-level invitation.
     */
    public function target(): Team|Workspace
    {
        return $this->team ?? $this->workspace;
    }

    /**
     * Accept this invitation on behalf of the given user : adding them to the team and
     * its parent workspace or just the workspace.
     */
    public function acceptFor(User $user): void
    {
        abort_unless($this->status === InvitationStatus::Pending && $this->email === $user->email, 403);

        if (! $this->workspace->hasMember($user)) {
            $this->workspace->members()->syncWithoutDetaching($user);
        }

        if ($this->team) {
            $this->team->members()->syncWithoutDetaching($user);
        }

        $this->update(['status' => InvitationStatus::Accepted]);
    }

    /**
     * Decline this invitation on behalf of the given user.
     */
    public function declineFor(User $user): void
    {
        abort_unless($this->status === InvitationStatus::Pending && $this->email === $user->email, 403);

        $this->update(['status' => InvitationStatus::Declined]);
    }
}
