<?php

use App\Livewire\Forms\Settings\ToolLinksForm;
use App\Models\Team;
use App\Models\Tool;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')]
class extends Component {
    public Team $team;

    public ToolLinksForm $form;

    public function mount(Team $team): void
    {
        Gate::authorize('manageListing', $team);

        $this->team = $team;
        $this->form->setTool($team->tool);
    }

    public function save(): void
    {
        $this->form->update();

        $this->dispatch('tool-saved');
    }

    #[Computed]
    public function tool(): ?Tool
    {
        return $this->team->tool;
    }
};
?>

<div class="flex h-full min-h-0 flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/teams/listing/links.breadcrumb_settings') => null,
            __('app/settings/teams/listing/links.breadcrumb_teams') => null,
            $team->name => null,
            __('app/settings/teams/listing/links.breadcrumb_listing') => route('settings.teams.listing.index', $team),
            __('app/settings/teams/listing/links.breadcrumb_links') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="tool-saved"/>

            <x-ui.button
                variant="outline"
                size="xs"
                icon="arrow-up-right-01"
                :label="__('app/settings/teams/listing/links.preview_page')"
                :href="$this->tool ? route('tools.show', $this->tool) : null"
                :disabled="! $this->tool"
                target="_blank"
            />

            <x-ui.button variant="primary" size="sm" :label="__('app/settings/teams/listing/links.save_changes')" wire:click="save"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="min-h-0 flex-1 overflow-y-auto">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
            <header class="flex flex-col gap-1 px-4">
                <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/teams/listing/links.heading') }}</h1>
            </header>

            <x-domain.app.settings.section>
                <x-domain.app.settings.section-content icon="globe-02" :label="__('app/settings/teams/listing/links.website_label')" :description="__('app/settings/teams/listing/links.website_description')">
                    <div class="flex w-64 flex-col gap-1">
                        <x-ui.input wire:model="form.websiteUrl"/>
                        @error('form.websiteUrl')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content icon="github" :label="__('app/settings/teams/listing/links.github_label')" :description="__('app/settings/teams/listing/links.github_description')">
                    <div class="flex w-64 flex-col gap-1">
                        <x-ui.input wire:model="form.githubUrl"/>
                        @error('form.githubUrl')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content icon="twitter" :label="__('app/settings/teams/listing/links.twitter_label')" :description="__('app/settings/teams/listing/links.twitter_description')">
                    <div class="flex w-64 flex-col gap-1">
                        <x-ui.input wire:model="form.twitterUrl"/>
                        @error('form.twitterUrl')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content icon="apple" :label="__('app/settings/teams/listing/links.app_store_label')" :description="__('app/settings/teams/listing/links.app_store_description')">
                    <div class="flex w-64 flex-col gap-1">
                        <x-ui.input wire:model="form.appStoreUrl"/>
                        @error('form.appStoreUrl')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content icon="google-play" :label="__('app/settings/teams/listing/links.play_store_label')" :description="__('app/settings/teams/listing/links.play_store_description')">
                    <div class="flex w-64 flex-col gap-1">
                        <x-ui.input wire:model="form.playStoreUrl"/>
                        @error('form.playStoreUrl')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>
            </x-domain.app.settings.section>
        </div>
    </div>

    <x-domain.app.settings.listing-nav
        :prev-href="route('settings.teams.listing.details', $team)"
        :prev-label="__('app/settings/teams/listing/links.nav_prev_label')"
        :next-href="route('settings.teams.listing.basics', $team)"
        :next-label="__('app/settings/teams/listing/links.nav_next_label')"
    />
</div>
