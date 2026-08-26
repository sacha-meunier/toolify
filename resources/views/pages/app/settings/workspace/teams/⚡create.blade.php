<?php

use App\Livewire\Forms\Settings\TeamCreateForm;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.settings')]
class extends Component {
    use WithFileUploads;

    public Workspace $workspace;

    public TeamCreateForm $form;

    public function mount(): void
    {
        $workspace = auth()->user()->currentWorkspace();

        abort_if(! $workspace, 404);

        Gate::authorize('createTeam', $workspace);

        $this->workspace = $workspace;
        $this->form->setWorkspace($workspace);
    }

    public function save(): void
    {
        $team = $this->form->store();

        $this->redirectRoute('settings.teams.general', $team);
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/workspace/teams/create.breadcrumb_settings') => null,
            __('app/settings/workspace/teams/create.breadcrumb_workspace') => null,
            __('app/settings/workspace/teams/create.breadcrumb_teams') => route('settings.workspace.teams.index'),
            __('app/settings/workspace/teams/create.breadcrumb_create') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" :label="__('app/settings/workspace/teams/create.create_team')" :disabled="! trim($form->name)" wire:click="save"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 border-b border-border px-4 pb-6">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/workspace/teams/create.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/workspace/teams/create.description', ['name' => $workspace->name]) }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/workspace/teams/create.logo_label')"
                :description="__('app/settings/workspace/teams/create.logo_description')"
            >
                <div class="flex items-center gap-3" x-data>
                    <div class="flex size-10 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-muted-foreground">
                        @if ($form->logo && $form->logo->isPreviewable())
                            <img src="{{ $form->logo->temporaryUrl() }}" alt="" class="size-full object-cover">
                        @else
                            <x-ui.icon.layer class="size-5"/>
                        @endif
                    </div>

                    <input type="file" wire:model="form.logo" accept="image/*" class="hidden" x-ref="logoInput">
                    <x-ui.button variant="outline" size="sm" :label="__('app/settings/workspace/teams/create.change_photo')" x-on:click="$refs.logoInput.click()"/>
                </div>

                @error('form.logo')
                <x-ui.field.error>{{ $message }}</x-ui.field.error>
                @enderror
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/workspace/teams/create.name_label')"
                :description="__('app/settings/workspace/teams/create.name_description')"
            >
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model.live="form.name" :placeholder="__('app/settings/workspace/teams/create.name_placeholder')"/>
                    @error('form.name')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>
    </div>
</div>
