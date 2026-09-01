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
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int|null $owner_id
 * @property string $name
 * @property string|null $logo_url
 * @property string $slug
 * @property string|null $invite_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'logo_url', 'owner_id', 'invite_code'])]
class Workspace extends Model
{
    /** @use HasFactory<WorkspaceFactory> */
    use HasFactory, HasStack, HasSurveys;

    public const int NAME_MAX_LENGTH = 75;

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

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
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

    /**
     * The total amount of users in a team, this includes the owner.
     */
    public function totalMemberCount(): int
    {
        return $this->members()->count() + ($this->owner_id ? 1 : 0);
    }

    /**
     * Get the workspace's initials, used as a logo fallback.
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Generate an invite code guaranteed to be unique : since collisions can't be predicted
     * ahead of a random generation, keep generating a new candidate until one isn't already taken.
     */
    public static function generateUniqueInviteCode(): string
    {
        $code = static::randomInviteCode();

        while (static::where('invite_code', $code)->exists()) {
            $code = static::randomInviteCode();
        }

        return $code;
    }

    /**
     * Generate a random "XXX-XXX" invite code candidate, not checked for uniqueness yet.
     */
    private static function randomInviteCode(): string
    {
        return Str::upper(Str::random(3)).'-'.Str::upper(Str::random(3));
    }

    /**
     * Generate a unique slug for a workspace, adding  a random suffix on collision.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);

        $unique = $slug;

        while (static::where('slug', $unique)->exists()) {
            $unique = $slug.'-'.random_int(1, 1000000);
        }

        return $unique;
    }

    /**
     * Create a new workspace owned by the given user, with a unique slug and invite code.
     */
    public static function createOwnedBy(User $user, string $name): static
    {
        return static::create([
            'name' => $name,
            'slug' => static::generateUniqueSlug($name),
            'owner_id' => $user->id,
            'invite_code' => static::generateUniqueInviteCode(),
        ]);
    }

    /**
     * Find the workspace matching a pasted invite code.
     */
    public static function findByInviteCode(string $code): ?static
    {
        return static::where('invite_code', Str::upper(trim($code)))->first();
    }
}
