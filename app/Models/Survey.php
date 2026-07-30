<?php

namespace App\Models;

use Database\Factories\SurveyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $owner_type
 * @property int $owner_id
 * @property string $name
 * @property string|null $query
 * @property array{pricing: array<string>, categories: array<string>, platforms: array<string>} $filters
 * @property Carbon|null $last_visited_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'query', 'filters'])]
class Survey extends Model
{
    /** @use HasFactory<SurveyFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'last_visited_at' => 'datetime',
        ];
    }

    /**
     * The single owner (User/Workspace/Team) this survey belongs to, fixed at creation.
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record that this survey was just run.
     */
    public function markVisited(): void
    {
        // `last_visited_at` is not fillable since it's never user-submitted.
        $this->forceFill(['last_visited_at' => now()])->save();
    }
}
