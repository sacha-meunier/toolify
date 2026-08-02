<?php

use App\Livewire\Forms\Settings\ProfileForm;
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
            'Settings' => null,
            'Profile' => null,
        ]"/>

        <x-slot:actions>
            <x-ui.field.saved event="profile-saved"/>
            <x-ui.button variant="primary" size="sm" label="Save changes" wire:click="save"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 border-b border-border px-4 pb-6">
            <h1 class="text-3xl font-semibold text-foreground">Profile</h1>
            <p class="text-sm text-muted-foreground">Manage your account information and public details.</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                label="Profile picture"
                description="Avatar displayed in member lists, comments, and notifications sent on your behalf."
            >
                <div class="flex items-center gap-3" x-data>
                    <div class="flex size-8 shrink-0 items-center justify-center overflow-clip rounded-full border border-border bg-muted text-xs font-medium text-muted-foreground">
                        @if ($form->avatar && $form->avatar->isPreviewable())
                            <img src="{{ $form->avatar->temporaryUrl() }}" alt="" class="size-full object-cover">
                        @elseif ($form->user->avatar_url)
                            <img src="{{ $form->user->avatar_url }}" alt="" class="size-full object-cover">
                        @else
                            {{ $form->user->initials() }}
                        @endif
                    </div>

                    <input type="file" wire:model="form.avatar" accept="image/*" class="hidden" x-ref="avatarInput">
                    <x-ui.button variant="outline" size="sm" label="Change photo" x-on:click="$refs.avatarInput.click()"/>

                    @if ($form->user->avatar_url)
                        <x-ui.button variant="ghost" size="sm" label="Remove" wire:click="deleteAvatar" wire:confirm="Remove your profile picture?"/>
                    @endif
                </div>

                @error('form.avatar')
                <x-ui.field.error>{{ $message }}</x-ui.field.error>
                @enderror
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                label="Full name"
                description="Name displayed on your profile and in invitations sent on your behalf."
            >
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input wire:model="form.name"/>
                    @error('form.name')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                label="Username"
                description="Unique identifier used in @mentions and in the profile URL (/u/username)."
            >
                <x-ui.input class="w-64" placeholder="Coming soon" disabled/>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                label="Email"
                description="Email address associated with the account. Used for login and notifications."
            >
                <div class="flex w-64 flex-col gap-1">
                    <x-ui.input type="email" wire:model="form.email"/>
                    @error('form.email')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>
    </div>
</div>
