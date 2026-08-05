<?php

use App\Enums\Pricing;
use App\Enums\ToolStatus;
use App\Enums\ToolVisibility;
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
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="['Settings' => null, 'Teams' => null, $team->name => null, 'Listing' => null]"/>

        <x-slot:actions>
            <x-ui.button
                variant="outline"
                size="xs"
                icon="arrow-up-right-01"
                label="Preview page"
                wire:navigate
                :href="$this->tool ? route('tools.show', $this->tool) : null"
                :disabled="! $this->tool"
                target="_blank"
            />
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10" wire:transition="content">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">Listing</h1>
            <p class="text-sm text-muted-foreground">Fill in your company's details to allow people to find your products.</p>
        </header>

        @if ($this->tool)
            <div wire:key="listing-populated" class="flex flex-col gap-8">
                <div class="flex items-center justify-between gap-6 rounded-xl border border-border bg-card px-4 py-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <x-ui.badge class="bg-foreground text-background">Live</x-ui.badge>

                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-foreground">toolify.app/{{ $team->workspace->slug }}/{{ $team->slug }}</p>
                            <p class="text-xs text-muted-foreground">Your app's detail page is complete.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="h-1 w-24 overflow-clip rounded-full bg-foreground/10">
                            <div class="h-full w-full rounded-full bg-foreground"></div>
                        </div>
                        <p class="shrink-0 text-xs text-muted-foreground">100% complete</p>
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

                <x-domain.app.settings.section label="General">
                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.identity', $team)" icon="discover-circle" label="Identity" description="Name, logo, tagline, about and categories" chevron>
                        <span class="flex items-center gap-1.5 text-xs font-medium text-foreground">
                            <span class="size-1.5 rounded-full bg-foreground"></span>
                            Complete
                        </span>
                    </x-domain.app.settings.section-content>

                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.details', $team)" icon="discover-circle" label="Details" description="Founded, HQ, headcount and status" chevron>
                        <span class="flex items-center gap-1.5 text-xs font-medium text-foreground">
                            <span class="size-1.5 rounded-full bg-foreground"></span>
                            Complete
                        </span>
                    </x-domain.app.settings.section-content>

                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.links', $team)" icon="discover-circle" label="Links" description="Website, repos, socials and resources" chevron>
                        <span class="text-xs font-medium text-muted-foreground">5 links</span>
                    </x-domain.app.settings.section-content>

                    {{--<x-domain.app.settings.section-content :href="route('settings.teams.listing.pricing', $team)" icon="discover-circle" label="Pricing" description="Free, basic, premium and custom plans" chevron>
                        <span class="text-xs font-medium text-muted-foreground">4 plans</span>
                    </x-domain.app.settings.section-content>--}}
                </x-domain.app.settings.section>

                <x-domain.app.settings.section label="Configuration">
                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.basics', $team)" icon="discover-circle" label="Basics" description="Public URL, visibility and listing state" chevron/>

                    <x-domain.app.settings.section-content :href="route('settings.teams.listing.danger-zone', $team)" icon="discover-circle" label="Danger zone" description="Unpublish, reset or delete this listing." chevron/>
                </x-domain.app.settings.section>
            </div>
        @else
            <div wire:key="listing-empty" class="flex flex-col gap-8">
                <x-domain.app.settings.section label="What you'll get" description="Listing {{ $team->name }} unlocks the following advantages:">
                    <x-domain.app.settings.section-content label="Discoverability" description="{{ $team->name }} becomes indexed in Search."/>
                    <x-domain.app.settings.section-content label="Reply to reviews" description="Engage publicly with the reviews left on {{ $team->name }}."/>
                    <x-domain.app.settings.section-content label="Followers feed" description="Followers receive your activity, updates, new releases and changelog entries in their inbox."/>
                </x-domain.app.settings.section>

                <x-domain.app.settings.section label="What gets published" description="Only the information filled in the listing is publicly available.">
                    <x-domain.app.settings.section-content label="Identity" description="Name, logo, tagline, description and categories become visible on {{ $team->name }}'s page."/>
                    <x-domain.app.settings.section-content label="Details" description="Founded year, headquarters, headcount and status appear as details on the page."/>
                    <x-domain.app.settings.section-content label="Links" description="Website, GitHub, X/Twitter, App Store and Play Store links become clickable on the page."/>
                </x-domain.app.settings.section>

                <x-domain.app.settings.section label="Ready when you are">
                    <x-domain.app.settings.section-content label="Setup the listing of {{ $team->name }}" description="Opens a guided flow to fill in your product's profile. You can save and resume at any time.">
                        <x-ui.button variant="primary" size="sm" label="Setup listing" wire:click="setupListing"/>
                    </x-domain.app.settings.section-content>
                </x-domain.app.settings.section>
            </div>
        @endif
    </div>
</div>
