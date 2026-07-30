<?php

namespace App\Models;

use App\Models\Concerns\HasStack;
use App\Models\Concerns\HasSurveys;
use Database\Factories\WorkspaceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $owner_id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'owner_id'])]
class Workspace extends Model
{
    /** @use HasFactory<WorkspaceFactory> */
    use HasFactory, HasStack, HasSurveys;

    /**
     * The user who owns this workspace. Null means the workspace is held by Toolify itself.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * The members of this workspace (not including the owner).
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Whether the given user can act on this workspace: its owner (deliberately never stored
     * in `members`, so removing a member can never accidentally strip ownership) or a member.
     */
    public function hasMember(User $user): bool
    {
        return $this->owner_id === $user->id || $this->members()->whereKey($user->id)->exists();
    }
}
