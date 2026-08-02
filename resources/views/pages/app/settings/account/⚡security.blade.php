<?php

use App\Livewire\Forms\Settings\DeleteAccountForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')]
class extends Component {
    public DeleteAccountForm $deleteAccountForm;

    public function mount(): void
    {
        $this->deleteAccountForm->setUser(Auth::user());
    }

    public function deleteAccount(): void
    {
        $this->deleteAccountForm->delete();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('login'), navigate: false);
    }
};
?>

<div class="flex flex-col" x-data="{ confirming: false }">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            'Settings' => null,
            'Account & Security' => null,
        ]"/>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 border-b border-border px-4 pb-6">
            <h1 class="text-3xl font-semibold text-foreground">Account & Security</h1>
            <p class="text-sm text-muted-foreground">Manage your credentials, connected accounts, and active
                sessions.</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                label="Password"
                description="Sends a reset password email to your address."
            >
                <a href="{{ route('password.request') }}" wire:navigate
                   class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary">
                    Change password
                </a>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                label="Two-factor authentication (2FA)"
                description="Adds a layer of security to your login. Once enabled, a code is required at every sign-in."
            >
                <div class="flex items-center gap-3">
                    <x-ui.badge>Coming soon</x-ui.badge>
                    <button disabled
                        class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary disabled:opacity-50">
                        Enable 2FA
                    </button>
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <x-domain.app.settings.section
            label="Connected accounts"
            description="Third-party accounts linked for login via OAuth."
        >
            <x-domain.app.settings.section-content icon="github" label="Github">
                <div class="flex items-center gap-3">
                    <x-ui.badge>Coming soon</x-ui.badge>
                    <button disabled
                            class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary disabled:opacity-50">
                        Connect
                    </button>
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content icon="google" label="Google">
                <div class="flex items-center gap-3">
                    <x-ui.badge>Coming soon</x-ui.badge>
                    <button disabled
                            class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary disabled:opacity-50">
                        Connect
                    </button>
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <x-domain.app.settings.section
            label="Active sessions"
            description="Devices and browsers currently logged into the account. Revoking a session immediately logs the device out."
        >
            <x-domain.app.settings.section-content
                icon="computer-terminal-01"
                label="Firefox on macOS"
                description="Brussels, BE · Active now"
            >
                <div class="flex items-center gap-3">
                    <x-ui.badge>Coming soon</x-ui.badge>
                    <x-ui.badge>Current</x-ui.badge>
                    <x-ui.button variant="outline" size="sm" label="Log out" disabled/>
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                icon="computer-terminal-01"
                label="Firefox on iPhone 13"
                description="Brussels, BE · Last seen 2 days ago"
            >
                <div class="flex items-center gap-3">
                    <x-ui.badge>Coming soon</x-ui.badge>
                    <x-ui.button variant="outline" size="sm" label="Revoke" disabled/>
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <section class="flex w-full flex-col gap-2">
            <header class="flex flex-col px-4">
                <p class="text-base font-semibold text-foreground">Delete account</p>
                <p class="text-sm text-muted-foreground">Permanently delete your account and all associated data. This
                    action cannot be undone.</p>
            </header>

            <div
                class="flex w-full flex-col divide-y divide-border overflow-clip rounded-xl border border-border bg-card shadow-xs">
                <x-domain.app.settings.section-content
                    label="Delete account"
                    description="Once deleted, all your data will be permanently removed. There is no going back."
                >
                    <x-ui.button variant="destructive" size="sm" label="Delete account" @click="confirming = true"/>
                </x-domain.app.settings.section-content>
            </div>
        </section>

        <div x-show="confirming" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                x-show="confirming"
                @click.outside="confirming = false"
                @keydown.escape.window="confirming = false"
                class="flex w-full max-w-md flex-col gap-5 rounded-lg border border-border bg-background p-6 shadow-lg"
            >
                <header class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold text-foreground">Delete account</h2>
                    <p class="text-sm text-muted-foreground">
                        This will permanently delete your account and all of its data. This action cannot be undone.
                    </p>
                </header>

                <div class="flex flex-col gap-1">
                    <x-ui.input type="password" autocomplete="new-password" aria-autocomplete="none"
                                placeholder="Confirm your password" wire:model="deleteAccountForm.password"
                                wire:keydown.enter="deleteAccount"/>
                    @error('deleteAccountForm.password')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <x-ui.input type="text" autocomplete="off" placeholder="Type DELETE to confirm"
                                wire:model="deleteAccountForm.confirmation" wire:keydown.enter="deleteAccount"/>
                    @error('deleteAccountForm.confirmation')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2">
                    <x-ui.button variant="outline" label="Cancel" @click="confirming = false"/>
                    <x-ui.button variant="destructive" label="Delete account" wire:click="deleteAccount"/>
                </div>
            </div>
        </div>
    </div>
</div>
