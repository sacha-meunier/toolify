<?php

use App\Livewire\Forms\Settings\TeamForm;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.settings')]
class extends Component {
    use WithFileUploads;

    public Team $team;

    public TeamForm $form;

    public function mount(Team $team): void
    {
        Gate::authorize('manageTeam', $team);

        $this->team = $team;
        $this->form->setTeam($team);
    }

    public function save(): void
    {
        $this->form->update();

        $this->dispatch('team-saved');
    }

    public function deleteLogo(): void
    {
        $this->form->deleteLogo();
    }

    public function deleteTeam(): void
    {
        Gate::authorize('manageTeam', $this->team);

        $this->team->delete();

        $this->redirectRoute('discovery');
    }
};
?>

<div class="flex flex-col" x-data="{ confirming: false }">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            'Settings' => null,
            'Teams' => null,
            $team->name => null,
            'General' => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="team-saved"/>
            <x-ui.button variant="primary" size="sm" label="Save changes" wire:click="save"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 border-b border-border px-4 pb-6">
            <h1 class="text-3xl font-semibold text-foreground">General</h1>
            <p class="text-sm text-muted-foreground">Manage your team's identity and general settings.</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                label="Logo"
                description="Displayed in the sidebar and team switcher."
            >
                <div class="flex items-center gap-3" x-data>
                    <div class="flex size-10 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-muted-foreground">
                        @if ($form->logo && $form->logo->isPreviewable())
                            <img src="{{ $form->logo->temporaryUrl() }}" alt="" class="size-full object-cover">
                        @elseif ($form->team->logo_url)
                            <img src="{{ $form->team->logo_url }}" alt="" class="size-full object-cover">
                        @else
                            <x-ui.icon.layer class="size-5"/>
                        @endif
                    </div>

                    <input type="file" wire:model="form.logo" accept="image/*" class="hidden" x-ref="logoInput">
                    <x-ui.button variant="outline" size="sm" label="Change photo" x-on:click="$refs.logoInput.click()"/>

                    @if ($form->team->logo_url)
                        <x-ui.button variant="ghost" size="sm" label="Remove" wire:click="deleteLogo" wire:confirm="Remove your team's logo?"/>
                    @endif
                </div>

                @error('form.logo')
                <x-ui.field.error>{{ $message }}</x-ui.field.error>
                @enderror
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                label="Name"
                description="Displayed across the app for you and your team members."
            >
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model="form.name"/>
                    @error('form.name')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <section class="flex w-full flex-col gap-2">
            <header class="flex flex-col px-4">
                <p class="text-base font-semibold text-foreground">Danger zone</p>
            </header>

            <div class="flex w-full flex-col divide-y divide-border overflow-clip rounded-xl border border-border bg-card shadow-xs">
                <x-domain.app.settings.section-content
                    label="Delete team"
                    description="Permanently removes this team, its stack, surveys, and all associated data. This action cannot be undone."
                >
                    <x-ui.button variant="destructive" size="sm" label="Delete team" @click="confirming = true"/>
                </x-domain.app.settings.section-content>
            </div>
        </section>

        <div x-show="confirming" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                x-show="confirming"
                @click.outside="confirming = false"
                @keydown.escape.window="confirming = false"
                class="flex w-full max-w-md flex-col gap-5 rounded-lg border border-border bg-background p-6 shadow-lg"
            >
                <header class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold text-foreground">Delete team</h2>
                    <p class="text-sm text-muted-foreground">
                        This will permanently delete {{ $team->name }} and all of its data — stack, surveys, and members. This action cannot be undone.
                    </p>
                </header>

                <div class="flex items-center justify-end gap-2">
                    <x-ui.button variant="outline" label="Cancel" @click="confirming = false"/>
                    <x-ui.button variant="destructive" label="Delete team" wire:click="deleteTeam"/>
                </div>
            </div>
        </div>
    </div>
</div>
