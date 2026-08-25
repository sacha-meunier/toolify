<?php

use App\Livewire\Forms\Settings\WorkspaceForm;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.settings')]
class extends Component {
    use WithFileUploads;

    public Workspace $workspace;

    public WorkspaceForm $form;

    public function mount(): void
    {
        $workspace = auth()->user()->currentWorkspace();

        abort_if(! $workspace, 404);

        Gate::authorize('manageWorkspace', $workspace);

        $this->workspace = $workspace;
        $this->form->setWorkspace($workspace);
    }

    public function save(): void
    {
        $this->form->update();

        $this->dispatch('workspace-saved');
    }

    public function deleteLogo(): void
    {
        $this->form->deleteLogo();
    }

    public function deleteWorkspace(): void
    {
        Gate::authorize('manageWorkspace', $this->workspace);

        $this->workspace->delete();

        $this->redirectRoute('discovery');
    }
};
?>

<div class="flex flex-col" x-data="{ confirming: false }">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/workspace/general.breadcrumb_settings') => null,
            __('app/settings/workspace/general.breadcrumb_workspace') => null,
            __('app/settings/workspace/general.breadcrumb_general') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="workspace-saved"/>
            <x-ui.button variant="primary" size="sm" :label="__('app/settings/workspace/general.save_changes')" wire:click="save"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 border-b border-border px-4 pb-6">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/workspace/general.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/workspace/general.description') }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/workspace/general.logo_label')"
                :description="__('app/settings/workspace/general.logo_description')"
            >
                <div class="flex items-center gap-3" x-data>
                    <div class="flex size-10 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-muted-foreground">
                        @if ($form->logo && $form->logo->isPreviewable())
                            <img src="{{ $form->logo->temporaryUrl() }}" alt="" class="size-full object-cover">
                        @elseif ($form->workspace->logo_url)
                            <img src="{{ $form->workspace->logo_url }}" alt="" class="size-full object-cover">
                        @else
                            <x-ui.icon.layer class="size-5"/>
                        @endif
                    </div>

                    <input type="file" wire:model="form.logo" accept="image/*" class="hidden" x-ref="logoInput">
                    <x-ui.button variant="outline" size="sm" :label="__('app/settings/workspace/general.change_photo')" x-on:click="$refs.logoInput.click()"/>

                    @if ($form->workspace->logo_url)
                        <x-ui.button variant="ghost" size="sm" :label="__('app/settings/workspace/general.remove')" wire:click="deleteLogo" wire::confirm="__('app/settings/workspace/general.remove_logo_confirm')"/>
                    @endif
                </div>

                @error('form.logo')
                <x-ui.field.error>{{ $message }}</x-ui.field.error>
                @enderror
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/workspace/general.name_label')"
                :description="__('app/settings/workspace/general.name_description')"
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
                <p class="text-base font-semibold text-foreground">{{ __('app/settings/workspace/general.danger_zone_title') }}</p>
            </header>

            <div class="flex w-full flex-col divide-y divide-border overflow-clip rounded-xl border border-border bg-card shadow-xs">
                <x-domain.app.settings.section-content
                    :label="__('app/settings/workspace/general.delete_workspace_label')"
                    :description="__('app/settings/workspace/general.delete_workspace_description')"
                >
                    <x-ui.button variant="destructive" size="sm" :label="__('app/settings/workspace/general.delete_workspace_button')" @click="confirming = true"/>
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
                    <h2 class="text-lg font-semibold text-foreground">{{ __('app/settings/workspace/general.delete_modal_heading') }}</h2>
                    <p class="text-sm text-muted-foreground">
                        {{ __('app/settings/workspace/general.delete_modal_description', ['name' => $workspace->name]) }}
                    </p>
                </header>

                <div class="flex items-center justify-end gap-2">
                    <x-ui.button variant="outline" :label="__('app/settings/workspace/general.cancel')" @click="confirming = false"/>
                    <x-ui.button variant="destructive" :label="__('app/settings/workspace/general.delete_workspace_button')" wire:click="deleteWorkspace"/>
                </div>
            </div>
        </div>
    </div>
</div>
