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

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            'Settings' => null,
            'Teams' => null,
            $team->name => null,
            'Listing' => route('settings.teams.listing.index', $team),
            'Links' => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="tool-saved"/>

            <x-ui.button
                variant="outline"
                size="xs"
                icon="arrow-up-right-01"
                label="Preview page"
                :href="$this->tool ? route('tools.show', $this->tool) : null"
                :disabled="! $this->tool"
                target="_blank"
            />

            <x-ui.button variant="primary" size="sm" label="Save changes" wire:click="save"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">Links</h1>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content icon="globe-02" label="Website" description="The link to the website of your product / tool.">
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model="form.websiteUrl"/>
                    @error('form.websiteUrl')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content icon="github" label="Github" description="The link to the Github repository of your product / tool.">
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model="form.githubUrl"/>
                    @error('form.githubUrl')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content icon="twitter" label="X / Twitter" description="The link to the X / Twitter account of your product / tool.">
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model="form.twitterUrl"/>
                    @error('form.twitterUrl')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content icon="apple" label="App Store" description="The link to App Store page of your product / tool.">
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model="form.appStoreUrl"/>
                    @error('form.appStoreUrl')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content icon="google-play" label="Play store" description="The link to Play Store page of your product / tool.">
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model="form.playStoreUrl"/>
                    @error('form.playStoreUrl')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <div class="flex items-center justify-between px-4">
            <a href="{{ route('settings.teams.listing.details', $team) }}" wire:navigate class="flex items-center gap-1 text-sm font-medium text-foreground hover:text-primary">
                <x-ui.icon.arrow-left-01 size="xs"/>
                Details
            </a>

            <a href="{{ route('settings.teams.listing.basics', $team) }}" wire:navigate class="flex items-center gap-1 text-sm font-medium text-foreground hover:text-primary">
                Basics
                <x-ui.icon.arrow-right-01 size="xs"/>
            </a>
        </div>
    </div>
</div>
