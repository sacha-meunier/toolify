<?php

namespace App\Models;

use App\Models\Concerns\HasStack;
use App\Models\Concerns\HasSurveys;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar_url
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'avatar_url', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasStack, HasSurveys, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * The workspaces owned by this user.
     */
    public function ownedWorkspaces(): HasMany
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    /**
     * The workspaces this user is a member of (excludes owned workspaces).
     */
    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class)->withTimestamps();
    }

    /**
     * The teams this user is a member of.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    /**
     * The workspaces this user owns or is a member of, owned ones first.
     */
    public function accessibleWorkspaces(): Collection
    {
        return $this->ownedWorkspaces->concat($this->workspaces)->unique('id')->values();
    }

    /**
     * The workspace the user is currently acting within (picked via the workspace switcher,
     * falling back to the first accessible workspace when nothing was picked yet).
     */
    public function currentWorkspace(): ?Workspace
    {
        $accessible = $this->accessibleWorkspaces();

        return $accessible->firstWhere('id', session('current_workspace_id')) ?? $accessible->first();
    }
}
