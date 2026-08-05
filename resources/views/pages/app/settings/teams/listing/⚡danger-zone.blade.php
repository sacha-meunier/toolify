<?php

use App\Livewire\Forms\Settings\ToolDangerZoneListingForm;
use App\Models\Team;
use App\Models\Tool;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')] class extends Component
{
    public Team $team;

    public ToolDangerZoneListingForm $form;

    public function mount(Team $team): void
    {
        Gate::authorize('manageListing', $team);

        $this->team = $team;
        $this->form->setTool($team->tool);
    }

    public function delete(): void
    {
        $this->form->delete();

        $this->redirectRoute('settings.teams.listing.index', ['team' => $this->team], navigate: true);
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
            'Settings' => null,
            'Teams' => null,
            $team->name => null,
            'Listing' => route('settings.teams.listing.index', $team),
            'Danger zone' => null,
        ]"/>

        <x-slot:actions>
            <x-ui.button
                variant="outline"
                size="xs"
                icon="arrow-up-right-01"
                label="Preview page"
                :href="$this->tool ? route('tools.show', $this->tool) : null"
                :disabled="! $this->tool"
                target="_blank"
            />
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="min-h-0 flex-1 overflow-y-auto">
        <div
            class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10"
            x-data="{ confirming: false }"
        >
            <header class="flex flex-col gap-1 px-4">
                <h1 class="text-3xl font-semibold text-foreground">Danger zone</h1>
            </header>

            <x-domain.app.settings.section>
                <x-domain.app.settings.section-content
                    label="Remove listing"
                    description="Removes your product's listing from search, browsing, and new stacks. It stays visible as a removed entry in stacks that already saved it."
                >
                    <x-ui.button variant="destructive" size="sm" label="Remove listing" @click="confirming = true"/>
                </x-domain.app.settings.section-content>
            </x-domain.app.settings.section>

            <div x-show="confirming" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                <div
                    x-show="confirming"
                    @click.outside="confirming = false"
                    @keydown.escape.window="confirming = false"
                    class="flex w-full max-w-md flex-col gap-5 rounded-lg border border-border bg-background p-6 shadow-lg"
                >
                    <header class="flex flex-col gap-1">
                        <h2 class="text-lg font-semibold text-foreground">Remove listing</h2>
                        <p class="text-sm text-muted-foreground">
                            This will remove {{ $team->name }}'s listing from search, browsing, and new stacks. It stays visible as a removed entry in stacks that already saved it.
                        </p>
                    </header>

                    <div class="flex flex-col gap-1">
                        <x-ui.input type="text" autocomplete="off" placeholder="Type DELETE to confirm" wire:model="form.confirmation" wire:keydown.enter="delete"/>
                        @error('form.confirmation')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <x-ui.button variant="outline" label="Cancel" @click="confirming = false"/>
                        <x-ui.button variant="destructive" label="Remove listing" wire:click="delete"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-domain.app.settings.listing-nav
        :prev-href="route('settings.teams.listing.basics', $team)"
        prev-label="Basics"
    />
</div>
