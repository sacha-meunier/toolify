<?php

use App\Enums\ToolHeadcount;
use App\Livewire\Forms\Settings\ToolDetailsForm;
use App\Models\Team;
use App\Models\Tool;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')] class extends Component
{
    public Team $team;

    public ToolDetailsForm $form;

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
            __('app/settings/teams/listing/details.breadcrumb_settings') => null,
            __('app/settings/teams/listing/details.breadcrumb_teams') => null,
            $team->name => null,
            __('app/settings/teams/listing/details.breadcrumb_listing') => route('settings.teams.listing.index', $team),
            __('app/settings/teams/listing/details.breadcrumb_details') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="tool-saved"/>

            <x-ui.button
                variant="outline"
                size="xs"
                icon="arrow-up-right-01"
                :label="__('app/settings/teams/listing/details.preview_page')"
                :href="$this->tool ? route('tools.show', $this->tool) : null"
                :disabled="! $this->tool"
                target="_blank"
            />

            <x-ui.button variant="primary" size="sm" :label="__('app/settings/teams/listing/details.save_changes')" wire:click="save"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="min-h-0 flex-1 overflow-y-auto">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
            <header class="flex flex-col gap-1 px-4">
                <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/teams/listing/details.heading') }}</h1>
            </header>

            <x-domain.app.settings.section>
                <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/details.founded_label')" :description="__('app/settings/teams/listing/details.founded_description')">
                    <div class="flex w-full flex-col gap-1 lg:w-64">
                        <x-ui.input type="text" wire:model="form.foundedYear"/>
                        @error('form.foundedYear')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/details.first_release_label')" :description="__('app/settings/teams/listing/details.first_release_description')">
                    <div class="flex w-full flex-col gap-1 lg:w-64">
                        <x-ui.input type="text" wire:model="form.firstReleaseYear"/>
                        @error('form.firstReleaseYear')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/details.headquarters_label')" :description="__('app/settings/teams/listing/details.headquarters_description')">
                    <div class="flex w-full flex-col gap-1 lg:w-64">
                        <x-ui.input wire:model="form.headquarters"/>
                        @error('form.headquarters')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/details.headcount_label')" :description="__('app/settings/teams/listing/details.headcount_description', ['name' => $team->name])">
                    <div
                        class="flex w-full flex-col gap-1 lg:w-64"
                        x-data="{
                            open: false,
                            position: { top: 0, left: 0, width: 0 },
                            options: [
                                @foreach (ToolHeadcount::cases() as $option)
                                    { value: '{{ $option->value }}', label: @js($option->label()) },
                                @endforeach
                            ],
                            label() {
                                return this.options.find(option => option.value === $wire.form.headcount)?.label ?? {{ Illuminate\Support\Js::from(__('app/settings/teams/listing/details.select_placeholder')) }};
                            },
                            toggle() {
                                const rect = $refs.trigger.getBoundingClientRect();
                                this.position = { top: rect.bottom + window.scrollY + 4, left: rect.left + window.scrollX, width: rect.width };
                                this.open = ! this.open;
                            },
                        }"
                    >
                        <button
                            type="button"
                            x-ref="trigger"
                            @click="toggle()"
                            class="flex h-8 w-full items-center justify-between rounded-lg border border-input bg-secondary px-2.5 text-left text-sm"
                        >
                            <span x-text="label()" :class="$wire.form.headcount ? 'text-foreground' : 'text-muted-foreground'"></span>
                            <x-ui.icon.arrow-down-01 size="xs" class="shrink-0 text-muted-foreground"/>
                        </button>

                        <template x-teleport="body">
                            <div
                                x-show="open"
                                x-cloak
                                x-transition
                                @click.outside="if (! $refs.trigger.contains($event.target)) open = false"
                                x-bind:style="`position: fixed; top: ${position.top}px; left: ${position.left}px; width: ${position.width}px;`"
                                class="z-50 overflow-clip rounded-lg border border-border bg-popover p-1 shadow-md"
                            >
                                <template x-for="option in options" :key="option.value">
                                    <button
                                        type="button"
                                        @click="$wire.form.headcount = option.value; open = false"
                                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm text-popover-foreground hover:bg-muted"
                                    >
                                        <span x-text="option.label"></span>
                                        <x-ui.icon.checkmark-circle-02 size="xs" class="text-foreground" x-show="$wire.form.headcount === option.value"/>
                                    </button>
                                </template>
                            </div>
                        </template>

                        @error('form.headcount')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>
            </x-domain.app.settings.section>
        </div>
    </div>

    <x-domain.app.settings.listing-nav
        :prev-href="route('settings.teams.listing.identity', $team)"
        :prev-label="__('app/settings/teams/listing/details.nav_prev_label')"
        :next-href="route('settings.teams.listing.links', $team)"
        :next-label="__('app/settings/teams/listing/details.nav_next_label')"
    />
</div>
