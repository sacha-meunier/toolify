<?php

use App\Enums\Category;
use App\Livewire\Forms\Settings\ToolIdentityForm;
use App\Livewire\Traits\BuildsPageTitle;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.settings')] class extends Component
{
    use BuildsPageTitle, WithFileUploads;

    public Team $team;

    public ToolIdentityForm $form;

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

    public function deleteLogo(): void
    {
        $this->form->deleteLogo();
    }

    public function render()
    {
        return $this->view()->title($this->pageTitle(__('app/settings/teams/listing/identity.heading'), $this->team->name));
    }
};
?>

<div class="flex h-full min-h-0 flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/teams/listing/identity.breadcrumb_settings') => null,
            __('app/settings/teams/listing/identity.breadcrumb_teams') => null,
            $team->name => null,
            __('app/settings/teams/listing/identity.breadcrumb_listing') => route('settings.teams.listing.index', $team),
            __('app/settings/teams/listing/identity.breadcrumb_identity') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="tool-saved"/>

            <x-ui.button
                variant="outline"
                size="sm"
                icon="arrow-up-right-01"
                :label="__('app/settings/teams/listing/identity.preview_page')"
                :href="$team->tool ? route('tools.show', $team->tool) : null"
                :disabled="! $team->tool"
                target="_blank"
            />

            <x-ui.button variant="primary" size="sm" :label="__('app/settings/teams/listing/identity.save_changes')" wire:click="save" x-bind:disabled="! ($wire.$dirty('form') || $wire.form.logo)"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="min-h-0 flex-1 overflow-y-auto">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
            <header class="flex flex-col gap-1 px-4">
                <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/teams/listing/identity.heading') }}</h1>
            </header>

            <x-domain.app.settings.section>
                <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/identity.logo_label')" :description="__('app/settings/teams/listing/identity.logo_description')">
                    <x-ui.avatar-picker
                        class="size-8 bg-muted text-muted-foreground"
                        preview-model="form.logo"
                        :has-preview="(bool) ($form->logo && $form->logo->isPreviewable())"
                        :has-persisted="(bool) $form->tool?->logo_url"
                        delete-action="deleteLogo"
                        :delete-confirm="__('app/settings/teams/listing/identity.remove_logo_confirm')"
                        :change-label="__('app/settings/teams/listing/identity.change_photo')"
                        :remove-label="__('app/settings/teams/listing/identity.remove')"
                    >
                        @if ($form->logo && $form->logo->isPreviewable())
                            <img src="{{ $form->logo->temporaryUrl() }}" alt="" class="size-full object-cover">
                        @elseif ($form->tool?->logo_url)
                            <img src="{{ $form->tool->logo_url }}" alt="" class="size-full object-cover">
                        @else
                            <x-ui.icon.command class="size-4"/>
                        @endif

                        <x-slot:input>
                            <input type="file" wire:model="form.logo" accept="image/*" class="hidden" x-ref="pickerInput">
                        </x-slot:input>
                    </x-ui.avatar-picker>

                    @error('form.logo')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </x-domain.app.settings.section-content>

                <x-domain.app.settings.section-content :label="__('app/settings/teams/listing/identity.name_label')" :description="__('app/settings/teams/listing/identity.name_description')" required>
                    <div class="flex w-full flex-col gap-1 lg:w-64" x-data="{ length: {{ mb_strlen($form->name) }} }">
                        <x-ui.input wire:model="form.name" x-on:input="length = $event.target.value.length" required maxlength="{{ ToolIdentityForm::NAME_MAX_LENGTH }}"/>
                        <x-ui.field.error x-show="length >= {{ ToolIdentityForm::NAME_MAX_LENGTH }}" x-cloak>
                            {{ __('components/ui/field.max_length_reached', ['max' => ToolIdentityForm::NAME_MAX_LENGTH]) }}
                        </x-ui.field.error>
                        @error('form.name')
                        <x-ui.field.error>{{ $message }}</x-ui.field.error>
                        @enderror
                    </div>
                </x-domain.app.settings.section-content>
            </x-domain.app.settings.section>

            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-2" x-data="{ activeLocale: '{{ app()->getLocale() }}' }">
                    <div class="flex flex-col items-start gap-2 px-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex flex-col gap-0.5">
                            <p class="text-sm font-medium text-foreground">{{ __('app/settings/teams/listing/identity.tagline_title') }}</p>
                            <p class="text-sm text-muted-foreground">{{ __('app/settings/teams/listing/identity.tagline_description') }}</p>
                        </div>

                        <div class="inline-flex shrink-0 rounded-md border border-input p-0.5">
                            @foreach (config('app.available_locales') as $code => $label)
                                <button
                                    type="button"
                                    @click="activeLocale = '{{ $code }}'"
                                    :class="activeLocale === '{{ $code }}' ? 'bg-muted text-foreground' : 'text-muted-foreground hover:text-foreground'"
                                    :aria-pressed="activeLocale === '{{ $code }}'"
                                    aria-label="{{ $label }}"
                                    class="relative rounded px-2.5 py-1 text-xs font-medium uppercase transition-colors"
                                >
                                    {{ $code }}
                                    @error("form.tagline.$code")
                                        <span class="absolute -top-0.5 -right-0.5 size-1.5 rounded-full bg-destructive"></span>
                                    @enderror
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @foreach (config('app.available_locales') as $code => $label)
                        <div x-show="activeLocale === '{{ $code }}'" x-cloak x-data="{ length: {{ mb_strlen($form->tagline[$code] ?? '') }} }">
                            <div class="flex h-8 w-full items-center rounded-lg border border-input bg-secondary focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50">
                                <input
                                    type="text"
                                    wire:model="form.tagline.{{ $code }}"
                                    x-on:input="length = $event.target.value.length"
                                    maxlength="{{ ToolIdentityForm::TAGLINE_MAX_LENGTH }}"
                                    placeholder="{{ __('app/settings/teams/listing/identity.tagline_placeholder') }}"
                                    class="w-full flex-1 rounded-lg bg-transparent px-4 py-1 text-sm leading-5 text-foreground placeholder-muted-foreground outline-none"
                                >
                            </div>
                            <x-ui.field.error x-show="length >= {{ ToolIdentityForm::TAGLINE_MAX_LENGTH }}" x-cloak class="px-4">
                                {{ __('components/ui/field.max_length_reached', ['max' => ToolIdentityForm::TAGLINE_MAX_LENGTH]) }}
                            </x-ui.field.error>
                            @error("form.tagline.$code")
                            <x-ui.field.error class="px-4">{{ $message }}</x-ui.field.error>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="flex flex-col gap-2" x-data="{ activeLocale: '{{ app()->getLocale() }}' }">
                    <div class="flex flex-col items-start gap-2 px-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex flex-col gap-0.5">
                            <p class="text-sm font-medium text-foreground">{{ __('app/settings/teams/listing/identity.about_title') }}</p>
                            <p class="text-sm text-muted-foreground">{{ __('app/settings/teams/listing/identity.about_description') }}</p>
                        </div>

                        <div class="inline-flex shrink-0 rounded-md border border-input p-0.5">
                            @foreach (config('app.available_locales') as $code => $label)
                                <button
                                    type="button"
                                    @click="activeLocale = '{{ $code }}'"
                                    :class="activeLocale === '{{ $code }}' ? 'bg-muted text-foreground' : 'text-muted-foreground hover:text-foreground'"
                                    :aria-pressed="activeLocale === '{{ $code }}'"
                                    aria-label="{{ $label }}"
                                    class="relative rounded px-2.5 py-1 text-xs font-medium uppercase transition-colors"
                                >
                                    {{ $code }}
                                    @error("form.description.$code")
                                        <span class="absolute -top-0.5 -right-0.5 size-1.5 rounded-full bg-destructive"></span>
                                    @enderror
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @foreach (config('app.available_locales') as $code => $label)
                        <div x-show="activeLocale === '{{ $code }}'" x-cloak>
                            <textarea
                                rows="4"
                                wire:model.live.debounce.300ms="form.description.{{ $code }}"
                                placeholder="{{ __('app/settings/teams/listing/identity.about_placeholder') }}"
                                class="w-full resize-none rounded-lg border border-input bg-secondary px-4 py-1.5 text-sm text-foreground placeholder-muted-foreground outline-none focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50"
                            ></textarea>
                            <p class="px-4 pt-1 text-right text-xs text-muted-foreground">
                                {{ __('app/settings/teams/listing/identity.about_character_count', [
                                    'current' => mb_strlen($form->description[$code] ?? ''),
                                    'max' => ToolIdentityForm::DESCRIPTION_MAX_LENGTH,
                                ]) }}
                            </p>
                            @error("form.description.$code")
                            <x-ui.field.error class="px-4">{{ $message }}</x-ui.field.error>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <div
                class="flex flex-col gap-2"
                x-data="{
                    open: false,
                    options: [
                        @foreach (collect(Category::cases())->sortBy(fn ($category) => $category->label()) as $category)
                            { value: '{{ $category->value }}', label: @js($category->label()) },
                        @endforeach
                    ],
                    toggle(value) {
                        $wire.form.categories = $wire.form.categories.includes(value)
                            ? $wire.form.categories.filter(v => v !== value)
                            : [...$wire.form.categories, value];
                    },
                    label(value) {
                        return this.options.find(option => option.value === value)?.label;
                    },
                }"
            >
                <div class="flex flex-col gap-0.5 px-4">
                    <p class="text-sm font-medium text-foreground">{{ __('app/settings/teams/listing/identity.categories_title') }}</p>
                    <p class="text-sm text-muted-foreground">{{ __('app/settings/teams/listing/identity.categories_description') }}</p>
                </div>

                <div class="relative" @click.outside="open = false">
                    <div
                        role="button"
                        tabindex="0"
                        @click="open = ! open"
                        @keydown.enter="open = ! open"
                        class="flex w-full items-center gap-1.5 rounded-lg border border-input bg-secondary py-1.5 pr-3 pl-4 text-left"
                    >
                        <div class="flex flex-1 flex-wrap items-center gap-1.5">
                            <template x-if="$wire.form.categories.length === 0">
                                <span class="flex h-6 items-center px-0.5 text-sm text-muted-foreground">{{ __('app/settings/teams/listing/identity.categories_placeholder') }}</span>
                            </template>

                            <template x-for="value in $wire.form.categories" :key="value">
                                <button
                                    type="button"
                                    @click.stop="toggle(value)"
                                    class="inline-flex items-center gap-1 rounded-md bg-muted px-2 py-1 text-xs font-medium text-foreground"
                                >
                                    <span x-text="label(value)"></span>
                                    <x-ui.icon.cancel-01 size="xs"/>
                                </button>
                            </template>
                        </div>

                        <x-ui.icon.arrow-down-01 size="xs" class="shrink-0 text-muted-foreground"/>
                    </div>

                    <x-ui.dropdown-panel
                        x-show="open"
                        origin="top"
                        class="absolute z-10 mt-1 max-h-64 w-full overflow-y-auto rounded-lg border border-border bg-popover p-1 shadow-md"
                    >
                        <template x-for="option in options" :key="option.value">
                            <button
                                type="button"
                                @click="toggle(option.value)"
                                class="flex w-full items-center justify-between rounded-sm px-3 py-2 text-left text-sm text-popover-foreground hover:bg-muted"
                            >
                                <span x-text="option.label"></span>
                                <x-ui.icon.checkmark-circle-02 size="xs" class="text-foreground" x-show="$wire.form.categories.includes(option.value)"/>
                            </button>
                        </template>
                    </x-ui.dropdown-panel>
                </div>

                @error('form.categories')
                <x-ui.field.error class="px-4">{{ $message }}</x-ui.field.error>
                @enderror
            </div>
        </div>
    </div>

    <x-domain.app.settings.listing-nav
        :next-href="route('settings.teams.listing.details', $team)"
        :next-label="__('app/settings/teams/listing/identity.nav_next_label')"
    />
</div>
