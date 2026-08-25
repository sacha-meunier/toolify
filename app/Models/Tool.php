<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Platform;
use App\Enums\Pricing;
use App\Enums\ToolHeadcount;
use App\Enums\ToolStatus;
use App\Enums\ToolVisibility;
use Database\Factories\ToolFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $team_id
 * @property string $name
 * @property string $slug
 * @property string $tagline Resolved for the current app locale, falling back to the default locale.
 * @property string $description Resolved for the current app locale, falling back to the default locale.
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
 * @property ToolVisibility $visibility
 * @property string|null $share_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'slug', 'tagline', 'description', 'website_url', 'github_url', 'twitter_url', 'app_store_url', 'play_store_url', 'logo_url', 'banner_url', 'gallery', 'categories', 'pricing', 'platforms', 'team_id', 'founded_year', 'first_release_year', 'headquarters', 'headcount', 'status', 'visibility'])]
class Tool extends Model
{
    /** @use HasFactory<ToolFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Generate the tool's unguessable share token up front, so it's ready
     * the moment its visibility is switched to Unlisted.
     */
    protected static function booted(): void
    {
        static::creating(function (Tool $tool): void {
            $tool->share_token ??= Str::random(32);
        });
    }

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
            'visibility' => ToolVisibility::class,
        ];
    }

    /**
     * The tagline, translated for the current app locale.
     */
    protected function tagline(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): string => $this->resolveTranslation($value),
            set: fn (string $value, array $attributes): string => $this->mergeTranslation($attributes['tagline'] ?? null, $value),
        );
    }

    /**
     * The description, translated for the current app locale.
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): string => $this->resolveTranslation($value),
            set: fn (string $value, array $attributes): string => $this->mergeTranslation($attributes['description'] ?? null, $value),
        );
    }

    /**
     * Resolve a translatable JSON attribute for the current app locale, falling back to
     * the app's default locale when the current locale hasn't been translated yet.
     */
    private function resolveTranslation(?string $json): string
    {
        $translations = json_decode($json ?? '', true) ?? [];

        return $translations[app()->getLocale()]
            ?? $translations[config('app.fallback_locale')]
            ?? '';
    }

    /**
     * Set the translation for the current app locale on a translatable JSON attribute,
     * preserving the translations already stored for other locales.
     */
    private function mergeTranslation(?string $json, string $value): string
    {
        $translations = json_decode($json ?? '', true) ?? [];
        $translations[app()->getLocale()] = $value;

        return json_encode($translations);
    }

    /**
     * The team where the tool listing belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Scope to tools listable by the given user: Public tools for anyone, Private/Unlisted
     * tools belongs to a team the user is a member of (or owns the workspace of).
     */
    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user) {
            $query->where('visibility', ToolVisibility::Public);

            if ($user) {
                $query->orWhereHas('team', fn (Builder $team) => $team
                    ->whereHas('members', fn (Builder $members) => $members->whereKey($user->id))
                    ->orWhereHas('workspace', fn (Builder $workspace) => $workspace->where('owner_id', $user->id))
                );
            }
        });
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
            ->visibleTo(auth()->user())
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('tagline->'.app()->getLocale(), 'like', "%{$search}%")
                ->orWhere('tagline->'.config('app.fallback_locale'), 'like', "%{$search}%")
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
