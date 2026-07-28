<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $owner_type
 * @property int $owner_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Stack extends Model
{
    /**
     * The owner (User, Workspace, or Team) this stack belongs to.
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The tools part of the stack.
     */
    public function tools(): BelongsToMany
    {
        return $this->belongsToMany(Tool::class, 'stack_tool')->withTimestamps();
    }
}
