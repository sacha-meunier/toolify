<?php

use App\Enums\Pricing;
use App\Enums\ToolStatus;
use App\Enums\ToolVisibility;
use App\Livewire\Forms\Settings\ToolGalleryForm;
use App\Models\Team;
use App\Models\Tool;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Transition;
use Livewire\Component;

new #[Layout('layouts::shells.settings')] class extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        Gate::authorize('manageListing', $team);

        $this->team = $team;
    }

    #[Transition(type: 'fade')]
    public function setupListing(): void
    {
        $attributes = [
            'name' => $this->team->name,
            'slug' => Str::slug($this->team->name),
            'tagline' => '',
            'description' => '',
            'website_url' => '',
            'github_url' => null,
            'twitter_url' => null,
            'app_store_url' => null,
            'play_store_url' => null,
            'logo_url' => null,
            'banner_url' => null,
            'gallery' => null,
            'categories' => [],
            'pricing' => Pricing::Free->value,
            'platforms' => [],
            'founded_year' => null,
            'first_release_year' => null,
            'headquarters' => null,
            'headcount' => null,
            'status' => ToolStatus::Active->value,
            'visibility' => ToolVisibility::default()->value,
        ];

        /* A previously removed listing keeps its row (team_id/slug stay unique), so
         * re-setting it up restores and resets that row instead of inserting a new one. */
        $existing = Tool::withTrashed()->firstWhere('team_id', $this->team->id);

        if ($existing) {
            $existing->restore();
            $existing->update($attributes);
        } else {
            Tool::create(['team_id' => $this->team->id, ...$attributes]);
        }
    }

    #[Computed]
    public function tool(): ?Tool
    {
        return $this->team->tool;
    }

    /**
     * How many of the identity fields (logo, tagline, about, categories) are filled in, and
     * whether the gap is specifically translations started but not finished for every locale.
     *
     * @return array{filled: int, total: int, missingTranslations: bool}
     */
    #[Computed]
    public function identityProgress(): array
    {
        $locales = array_keys(config('app.available_locales'));
        $tagline = $this->tool?->translations('tagline') ?? [];
        $description = $this->tool?->translations('description') ?? [];

        $taglineComplete = collect($locales)->every(fn (string $locale) => filled($tagline[$locale] ?? null));
        $descriptionComplete = collect($locales)->every(fn (string $locale) => filled($description[$locale] ?? null));

        $fields = [
            (bool) $this->tool?->logo_url,
            $taglineComplete,
            $descriptionComplete,
            (bool) $this->tool?->categories?->isNotEmpty(),
        ];

        return [
            'filled' => collect($fields)->filter()->count(),
            'total' => count($fields),
            'missingTranslations' => (! $taglineComplete && collect($tagline)->filter(fn ($value) => filled($value))->isNotEmpty())
                || (! $descriptionComplete && collect($description)->filter(fn ($value) => filled($value))->isNotEmpty()),
        ];
    }

    /**
     * How many of the details fields (founded, first release, headquarters, headcount) are filled in.
     *
     * @return array{filled: int, total: int}
     */
    #[Computed]
    public function detailsProgress(): array
    {
        $fields = [
            (bool) $this->tool?->founded_year,
            (bool) $this->tool?->first_release_year,
            (bool) $this->tool?->headquarters,
            (bool) $this->tool?->headcount,
        ];

        return ['filled' => collect($fields)->filter()->count(), 'total' => count($fields)];
    }

    /**
     * How many of the link fields (website, GitHub, X/Twitter, App Store, Play Store) are filled in.
     *
     * @return array{filled: int, total: int}
     */
    #[Computed]
    public function linksProgress(): array
    {
        $fields = [
            (bool) $this->tool?->website_url,
            (bool) $this->tool?->github_url,
            (bool) $this->tool?->twitter_url,
            (bool) $this->tool?->app_store_url,
            (bool) $this->tool?->play_store_url,
        ];

        return ['filled' => collect($fields)->filter()->count(), 'total' => count($fields)];
    }

    /**
     * How many gallery images the listing currently has.
     *
     * @return array{filled: int, max: int}
     */
    #[Computed]
    public function galleryProgress(): array
    {
        return ['filled' => $this->tool?->gallery?->count() ?? 0, 'max' => ToolGalleryForm::MAX_IMAGES];
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[__('app/settings/teams/listing/index.breadcrumb_settings') => null, __('app/settings/teams/listing/index.breadcrumb_teams') => null, $team->name => null, __('app/settings/teams/listing/index.breadcrumb_listing') => null]"/>

        <x-slot:actions>
            <x-ui.button
                variant="outline"
                size="sm"
                icon="arrow-up-right-01"
                :label="__('app/settings/teams/listing/index.preview_page')"
                wire:navigate
                :href="$this->tool ? route('tools.show', $this->tool) : null"
                :disabled="! $this->tool"
                target="_blank"
            />
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10" wire:transition="content">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/teams/listing/index.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/teams/listing/index.description') }}</p>
        </header>

        @if ($this->tool)
            <div wire:key="listing-populated" class="flex flex-col gap-8">
                <div class="flex flex-col gap-2 rounded-xl border border-border bg-card px-4 py-4 shadow-xs lg:flex-row lg:items-center lg:gap-3 lg:py-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="flex size-8 shrink-0 items-center justify-center overflow-clip rounded-lg bg-muted text-xs font-medium text-muted-foreground">
                            @if ($this->tool->logo_url)
                                <img src="{{ $this->tool->logo_url }}" alt="" class="size-full object-cover">
                            @else
                                {{ $this->tool->initials() }}
                            @endif
                        </span>

                        <p class="min-w-0 truncate text-sm font-medium text-foreground lg:hidden">{{ $this->tool->name }}</p>
                    </div>

                    <div class="flex min-w-0 flex-col gap-0.5 pl-11 lg:pl-0">
                        <p class="hidden min-w-0 items-center gap-1 text-sm font-medium text-foreground lg:flex">
                            <span class="truncate">{{ $this->tool->name }}</span>
                            <span class="shrink-0 text-muted-foreground">·</span>
                            <span class="truncate">toolify.app/tools/{{ $this->tool->slug }}</span>
                        </p>
                        <p class="truncate text-sm font-medium text-foreground lg:hidden">toolify.app/tools/{{ $this->tool->slug }}</p>
                        <p class="text-xs text-muted-foreground">{{ __('app/settings/teams/listing/index.visibility_status_'.$this->tool->visibility->value) }}</p>
                    </div>
                </div>

                {{--<x-domain.app.settings.section label="Hierarchy">
                    <div class="flex items-center gap-2 px-4 py-3">
                        <x-ui.icon.command class="size-3.5 shrink-0 text-foreground"/>
                        <p class="flex-1 truncate text-sm text-foreground">{{ $team->workspace->name }}</p>
                        <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 text-muted-foreground"/>
                    </div>

                    <div class="flex items-center gap-2 bg-muted py-3 pr-4 pl-8">
                        <x-ui.icon.command class="size-3.5 shrink-0 text-foreground"/>
                        <p class="flex-1 truncate text-sm font-medium text-foreground">{{ $team->name }}</p>
                        <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 text-muted-foreground"/>
                    </div>

                    @foreach ($this->tool->platforms as $platform)
                        <div class="flex items-center gap-2 py-3 pr-4 pl-12">
                            <x-ui.icon.command class="size-3.5 shrink-0 text-foreground"/>
                            <p class="flex-1 truncate text-sm text-foreground">{{ $platform->label() }}</p>
                            <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 text-muted-foreground"/>
                        </div>
                    @endforeach
                </x-domain.app.settings.section>--}}

                <x-domain.app.settings.section :label="__('app/settings/teams/listing/index.general_section_label')">
                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.identity', $team)" icon="identity-card" :label="__('app/settings/teams/listing/index.identity_label')" :description="__('app/settings/teams/listing/index.identity_description')" chevron>
                        @if ($this->identityProgress['filled'] === $this->identityProgress['total'])
                            <span class="flex items-center gap-1.5 text-xs font-medium text-success">
                                <span class="size-1.5 rounded-full bg-success"></span>
                                {{ __('app/settings/teams/listing/index.complete_status') }}
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-xs font-medium text-warning">
                                <span class="size-1.5 rounded-full bg-warning"></span>
                                {{ __('app/settings/teams/listing/index.progress_status', ['filled' => $this->identityProgress['filled'], 'total' => $this->identityProgress['total']]) }}
                                @if ($this->identityProgress['missingTranslations'])
                                    · {{ __('app/settings/teams/listing/index.missing_translations') }}
                                @endif
                            </span>
                        @endif
                    </x-domain.app.settings.section-content>

                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.details', $team)" icon="building-01" :label="__('app/settings/teams/listing/index.details_label')" :description="__('app/settings/teams/listing/index.details_description')" chevron>
                        @if ($this->detailsProgress['filled'] === $this->detailsProgress['total'])
                            <span class="flex items-center gap-1.5 text-xs font-medium text-success">
                                <span class="size-1.5 rounded-full bg-success"></span>
                                {{ __('app/settings/teams/listing/index.complete_status') }}
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-xs font-medium text-warning">
                                <span class="size-1.5 rounded-full bg-warning"></span>
                                {{ __('app/settings/teams/listing/index.progress_status', ['filled' => $this->detailsProgress['filled'], 'total' => $this->detailsProgress['total']]) }}
                            </span>
                        @endif
                    </x-domain.app.settings.section-content>

                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.links', $team)" icon="globe-02" :label="__('app/settings/teams/listing/index.links_label')" :description="__('app/settings/teams/listing/index.links_description')" chevron>
                        <span @class([
                            'text-xs font-medium',
                            'text-warning' => $this->linksProgress['filled'] !== $this->linksProgress['total'],
                            'text-muted-foreground' => $this->linksProgress['filled'] === $this->linksProgress['total'],
                        ])>
                            {{ __('app/settings/teams/listing/index.links_count', ['filled' => $this->linksProgress['filled'], 'total' => $this->linksProgress['total']]) }}
                        </span>
                    </x-domain.app.settings.section-content>

                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.gallery', $team)" icon="image-02" :label="__('app/settings/teams/listing/index.gallery_label')" :description="__('app/settings/teams/listing/index.gallery_description')" chevron>
                        <span class="text-xs font-medium text-muted-foreground">
                            {{ __('app/settings/teams/listing/index.gallery_count', ['filled' => $this->galleryProgress['filled'], 'max' => $this->galleryProgress['max']]) }}
                        </span>
                    </x-domain.app.settings.section-content>
                </x-domain.app.settings.section>

                <x-domain.app.settings.section :label="__('app/settings/teams/listing/index.configuration_section_label')">
                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.basics', $team)" icon="eye" :label="__('app/settings/teams/listing/index.basics_label')" :description="__('app/settings/teams/listing/index.basics_description')" chevron>
                        <span class="text-xs font-medium text-muted-foreground">{{ $this->tool->visibility->label() }} · {{ $this->tool->status->label() }}</span>
                    </x-domain.app.settings.section-content>

                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.danger-zone', $team)" icon="delete-02" :label="__('app/settings/teams/listing/index.danger_zone_label')" :description="__('app/settings/teams/listing/index.danger_zone_description')" chevron/>
                </x-domain.app.settings.section>
            </div>
        @else
            <div wire:key="listing-empty" class="flex flex-col gap-8">
                <x-domain.app.settings.section :label="__('app/settings/teams/listing/index.published_section_label')" :description="__('app/settings/teams/listing/index.published_section_description')">
                    <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/index.identity_label')" :description="__('app/settings/teams/listing/index.published_identity_description', ['name' => $team->name])"/>
                    <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/index.details_label')" :description="__('app/settings/teams/listing/index.published_details_description')"/>
                    <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/index.links_label')" :description="__('app/settings/teams/listing/index.published_links_description')"/>
                    <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/index.gallery_label')" :description="__('app/settings/teams/listing/index.published_gallery_description')"/>
                </x-domain.app.settings.section>

                <x-domain.app.settings.section :label="__('app/settings/teams/listing/index.ready_section_label')">
                    <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/index.setup_listing_label', ['name' => $team->name])" :description="__('app/settings/teams/listing/index.setup_listing_description')">
                        <x-ui.button variant="primary" size="sm" :label="__('app/settings/teams/listing/index.setup_listing_button')" wire:click="setupListing"/>
                    </x-domain.app.settings.section-content>
                </x-domain.app.settings.section>
            </div>
        @endif
    </div>
</div>
