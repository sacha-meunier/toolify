<?php

use App\Livewire\Forms\Settings\TeamForm;
use App\Livewire\Traits\BuildsPageTitle;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.settings')]
class extends Component {
    use BuildsPageTitle, WithFileUploads;

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

    public function render()
    {
        return $this->view()->title($this->pageTitle(__('app/settings/teams/general.heading'), $this->team->name));
    }
};
?>

<div class="flex flex-col" x-data="{ confirming: false }">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/teams/general.breadcrumb_settings') => null,
            __('app/settings/teams/general.breadcrumb_teams') => null,
            $team->name => null,
            __('app/settings/teams/general.breadcrumb_general') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="team-saved"/>
            <x-ui.button variant="primary" size="sm" :label="__('app/settings/teams/general.save_changes')" wire:click="save" x-bind:disabled="! ($wire.$dirty('form') || $wire.form.logo)"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/teams/general.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/teams/general.description') }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/teams/general.logo_label')"
                :description="__('app/settings/teams/general.logo_description')"
            >
                <x-ui.avatar-picker
                    class="size-8 bg-muted text-muted-foreground"
                    preview-model="form.logo"
                    :has-preview="(bool) ($form->logo && $form->logo->isPreviewable())"
                    :has-persisted="(bool) $form->team->logo_url"
                    delete-action="deleteLogo"
                    :delete-confirm="__('app/settings/teams/general.remove_logo_confirm')"
                    :change-label="__('app/settings/teams/general.change_photo')"
                    :remove-label="__('app/settings/teams/general.remove')"
                >
                    @if ($form->logo && $form->logo->isPreviewable())
                        <img src="{{ $form->logo->temporaryUrl() }}" alt="" class="size-full object-cover">
                    @elseif ($form->team->logo_url)
                        <img src="{{ $form->team->logo_url }}" alt="" class="size-full object-cover">
                    @else
                        <x-ui.icon.layer class="size-4"/>
                    @endif

                    <x-slot:input>
                        <input type="file" wire:model="form.logo" accept="image/*" class="hidden" x-ref="pickerInput">
                    </x-slot:input>
                </x-ui.avatar-picker>

                @error('form.logo')
                <x-ui.field.error>{{ $message }}</x-ui.field.error>
                @enderror
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/teams/general.name_label')"
                :description="__('app/settings/teams/general.name_description')"
                required
            >
                <div class="flex w-full flex-col gap-1 lg:w-64" x-data="{ length: {{ mb_strlen($form->name) }} }">
                    <x-ui.input wire:model="form.name" x-on:input="length = $event.target.value.length" required maxlength="{{ Team::NAME_MAX_LENGTH }}"/>
                    <x-ui.field.error x-show="length >= {{ Team::NAME_MAX_LENGTH }}" x-cloak>
                        {{ __('components/ui/field.max_length_reached', ['max' => Team::NAME_MAX_LENGTH]) }}
                    </x-ui.field.error>
                    @error('form.name')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <section class="flex w-full flex-col gap-2">
            <header class="flex flex-col px-4">
                <p class="text-base font-semibold text-foreground">{{ __('app/settings/teams/general.danger_zone_title') }}</p>
            </header>

            <div class="flex w-full flex-col divide-y divide-border overflow-clip rounded-xl border border-border bg-card shadow-xs">
                <x-domain.app.settings.section-content
                    :label="__('app/settings/teams/general.delete_team_label')"
                    :description="__('app/settings/teams/general.delete_team_description')"
                >
                    <x-ui.button variant="destructive" size="sm" :label="__('app/settings/teams/general.delete_team_button')" @click="confirming = true"/>
                </x-domain.app.settings.section-content>
            </div>
        </section>

        <x-ui.confirm-modal
            :heading="__('app/settings/teams/general.delete_modal_heading')"
            :description="__('app/settings/teams/general.delete_modal_description', ['name' => $team->name])"
            :cancel-label="__('app/settings/teams/general.cancel')"
        >
            <x-ui.button variant="destructive" :label="__('app/settings/teams/general.delete_team_button')" wire:click="deleteTeam"/>
        </x-ui.confirm-modal>
    </div>
</div>
