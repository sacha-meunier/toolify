<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Platform;
use App\Enums\Pricing;
use Database\Factories\ToolFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $team_id
 * @property string $name
 * @property string $slug
 * @property string $tagline
 * @property string $description
 * @property string $website_url
 * @property string|null $logo_url
 * @property Collection<int, Category> $categories
 * @property Pricing $pricing
 * @property Collection<int, Platform> $platforms
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'tagline', 'description', 'website_url', 'logo_url', 'categories', 'pricing', 'platforms', 'team_id'])]
class Tool extends Model
{
    /** @use HasFactory<ToolFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'categories' => AsEnumCollection::of(Category::class),
            'pricing' => Pricing::class,
            'platforms' => AsEnumCollection::of(Platform::class),
        ];
    }

    /**
     * The team where the tool listing belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
