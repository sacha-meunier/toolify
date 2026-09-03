<?php

use App\Livewire\Forms\Settings\ProfileForm;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.settings')]
class extends Component {
    use WithFileUploads;

    public ProfileForm $form;

    public function mount(): void
    {
        $this->form->setUser(auth()->user());
    }

    public function save(): void
    {
        $this->form->update();

        $this->dispatch('profile-saved');
    }

    public function deleteAvatar(): void
    {
        $this->form->deleteAvatar();
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/account/profile.breadcrumb_settings') => null,
            __('app/settings/account/profile.breadcrumb_profile') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="profile-saved"/>
            <x-ui.button variant="primary" size="sm" :label="__('app/settings/account/profile.save_changes')" wire:click="save" x-bind:disabled="! ($wire.$dirty('form') || $wire.form.avatar)"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/account/profile.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/account/profile.description') }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/account/profile.picture_label')"
                :description="__('app/settings/account/profile.picture_description')"
            >
                <x-ui.avatar-picker
                    class="size-8 bg-muted text-xs font-medium text-muted-foreground"
                    preview-model="form.avatar"
                    :has-preview="(bool) ($form->avatar && $form->avatar->isPreviewable())"
                    :has-persisted="(bool) $form->user->avatar_url"
                    delete-action="deleteAvatar"
                    :delete-confirm="__('app/settings/account/profile.remove_avatar_confirm')"
                    :change-label="__('app/settings/account/profile.change_photo')"
                    :remove-label="__('app/settings/account/profile.remove')"
                >
                    @if ($form->avatar && $form->avatar->isPreviewable())
                        <img src="{{ $form->avatar->temporaryUrl() }}" alt="" class="size-full object-cover">
                    @elseif ($form->user->avatar_url)
                        <img src="{{ $form->user->avatar_url }}" alt="" class="size-full object-cover">
                    @else
                        {{ $form->user->initials() }}
                    @endif

                    <x-slot:input>
                        <input type="file" wire:model="form.avatar" accept="image/*" class="hidden" x-ref="pickerInput">
                    </x-slot:input>
                </x-ui.avatar-picker>

                @error('form.avatar')
                <x-ui.field.error>{{ $message }}</x-ui.field.error>
                @enderror
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/account/profile.name_label')"
                :description="__('app/settings/account/profile.name_description')"
                required
            >
                <div class="flex w-full flex-col gap-1 lg:w-64" x-data="{ length: {{ mb_strlen($form->name) }} }">
                    <x-ui.input wire:model="form.name" x-on:input="length = $event.target.value.length" required maxlength="{{ User::NAME_MAX_LENGTH }}"/>
                    <x-ui.field.error x-show="length >= {{ User::NAME_MAX_LENGTH }}" x-cloak>
                        {{ __('components/ui/field.max_length_reached', ['max' => User::NAME_MAX_LENGTH]) }}
                    </x-ui.field.error>
                    @error('form.name')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/account/profile.username_label')"
                :description="__('app/settings/account/profile.username_description')"
            >
                <x-ui.input class="w-full lg:w-64" :placeholder="__('app/settings/account/profile.username_placeholder')" disabled/>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/account/profile.email_label')"
                :description="__('app/settings/account/profile.email_description')"
                required
            >
                <div class="flex w-full flex-col gap-1 lg:w-64">
                    <x-ui.input type="email" wire:model="form.email" required/>
                    @error('form.email')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>
    </div>
</div>
