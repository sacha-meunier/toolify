<?php

namespace App\Models;

use App\Models\Concerns\HasStack;
use App\Models\Concerns\HasSurveys;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $workspace_id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'workspace_id'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory, HasStack, HasSurveys;

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function tool(): HasOne
    {
        return $this->hasOne(Tool::class);
    }

    /**
     * The members of this team.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Whether the given user can act on this team: its parent workspace's owner (implicitly
     * has access to every team in their workspace) or a member of this team.
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->whereKey($user->id)->exists() || $this->workspace->owner_id === $user->id;
    }
}
