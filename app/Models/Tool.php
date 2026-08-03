<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Platform;
use App\Enums\Pricing;
use App\Enums\ToolHeadcount;
use App\Enums\ToolStatus;
use Database\Factories\ToolFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
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
 * @property string|null $github_url
 * @property string|null $twitter_url
 * @property string|null $app_store_url
 * @property string|null $play_store_url
 * @property string|null $logo_url
 * @property string|null $banner_url
 * @property Collection<int, string>|null $gallery
 * @property Collection<int, Category> $categories
 * @property Pricing $pricing
 * @property Collection<int, Platform> $platforms
 * @property int|null $founded_year
 * @property int|null $first_release_year
 * @property string|null $headquarters
 * @property ToolHeadcount|null $headcount
 * @property ToolStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'slug', 'tagline', 'description', 'website_url', 'github_url', 'twitter_url', 'app_store_url', 'play_store_url', 'logo_url', 'banner_url', 'gallery', 'categories', 'pricing', 'platforms', 'team_id', 'founded_year', 'first_release_year', 'headquarters', 'headcount', 'status'])]
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
            'gallery' => AsCollection::class,
            'headcount' => ToolHeadcount::class,
            'status' => ToolStatus::class,
        ];
    }

    /**
     * The team where the tool listing belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Tools matching a free-text search and a set of pricing/categories/platforms filters,
     * shared by the search page and by surveys (saved searches) re-running their own query.
     *
     * @param  array{pricing: array<string>, categories: array<string>, platforms: array<string>}  $filters
     */
    public static function matching(string $search, array $filters): Builder
    {
        $matchingCategories = collect(Category::cases())
            ->filter(fn (Category $category) => str_contains(strtolower($category->label()), strtolower($search)))
            ->map(fn (Category $category) => $category->value);

        return static::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('tagline', 'like', "%{$search}%")
                ->when($matchingCategories->isNotEmpty(), fn ($query) => $query->orWhere(
                    fn ($query) => $matchingCategories->each(
                        fn (string $category) => $query->orWhereJsonContains('categories', $category)
                    )
                ))
            )
            ->when($filters['pricing'] !== [], fn ($query) => $query->whereIn('pricing', $filters['pricing']))
            ->when($filters['categories'] !== [], fn ($query) => $query->where(
                fn ($query) => collect($filters['categories'])->each(
                    fn (string $category) => $query->orWhereJsonContains('categories', $category)
                )
            ))
            ->when($filters['platforms'] !== [], fn ($query) => $query->where(
                fn ($query) => collect($filters['platforms'])->each(
                    fn (string $platform) => $query->orWhereJsonContains('platforms', $platform)
                )
            ));
    }
}
