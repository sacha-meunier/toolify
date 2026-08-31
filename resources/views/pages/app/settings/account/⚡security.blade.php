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
            __('app/settings/account/security.breadcrumb_settings') => null,
            __('app/settings/account/security.breadcrumb_security') => null,
        ]"/>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/account/security.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/account/security.description') }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/account/security.password_label')"
                :description="__('app/settings/account/security.password_description')"
            >
                <a href="{{ route('password.request') }}" wire:navigate
                   class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary">
                    {{ __('app/settings/account/security.change_password') }}
                </a>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/account/security.two_factor_label')"
                :description="__('app/settings/account/security.two_factor_description')"
            >
                <div class="flex items-center gap-3">
                    <x-ui.badge>{{ __('app/settings/account/security.coming_soon') }}</x-ui.badge>
                    <button disabled
                        class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary disabled:opacity-50">
                        {{ __('app/settings/account/security.enable_2fa') }}
                    </button>
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <x-domain.app.settings.section
            :label="__('app/settings/account/security.connected_accounts_label')"
            :description="__('app/settings/account/security.connected_accounts_description')"
        >
            <x-domain.app.settings.section-content icon="github" :label="__('app/settings/account/security.github')">
                <div class="flex items-center gap-3">
                    <x-ui.badge>{{ __('app/settings/account/security.coming_soon') }}</x-ui.badge>
                    <button disabled
                            class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary disabled:opacity-50">
                        {{ __('app/settings/account/security.connect') }}
                    </button>
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content icon="google" :label="__('app/settings/account/security.google')">
                <div class="flex items-center gap-3">
                    <x-ui.badge>{{ __('app/settings/account/security.coming_soon') }}</x-ui.badge>
                    <button disabled
                            class="text-sm font-medium text-foreground underline underline-offset-4 hover:text-primary disabled:opacity-50">
                        {{ __('app/settings/account/security.connect') }}
                    </button>
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

        <section class="flex w-full flex-col gap-2">
            <header class="flex flex-col px-4">
                <p class="text-base font-semibold text-foreground">{{ __('app/settings/account/security.delete_account_section_title') }}</p>
                <p class="text-sm text-muted-foreground">{{ __('app/settings/account/security.delete_account_section_description') }}</p>
            </header>

            <div
                class="flex w-full flex-col divide-y divide-border overflow-clip rounded-xl border border-border bg-card shadow-xs">
                <x-domain.app.settings.section-content
                    :label="__('app/settings/account/security.delete_account_label')"
                    :description="__('app/settings/account/security.delete_account_description')"
                >
                    <x-ui.button variant="destructive" size="sm" :label="__('app/settings/account/security.delete_account_button')" @click="confirming = true"/>
                </x-domain.app.settings.section-content>
            </div>
        </section>

        <div x-show="confirming" x-cloak x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div
                x-show="confirming"
                @click.outside="confirming = false"
                @keydown.escape.window="confirming = false"
                class="flex w-full max-w-md flex-col gap-5 rounded-lg border border-border bg-popover p-6 shadow-lg"
            >
                <header class="flex flex-col gap-1">
                    <h2 class="text-lg font-semibold text-foreground">{{ __('app/settings/account/security.delete_modal_heading') }}</h2>
                    <p class="text-sm text-muted-foreground">
                        {{ __('app/settings/account/security.delete_modal_description') }}
                    </p>
                </header>

                <div class="flex flex-col gap-1">
                    <x-ui.input type="password" autocomplete="new-password" aria-autocomplete="none"
                                :placeholder="__('app/settings/account/security.password_placeholder')" wire:model="deleteAccountForm.password"
                                wire:keydown.enter="deleteAccount"/>
                    @error('deleteAccountForm.password')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <x-ui.input type="text" autocomplete="off" :placeholder="__('app/settings/account/security.confirmation_placeholder')"
                                wire:model="deleteAccountForm.confirmation" wire:keydown.enter="deleteAccount"/>
                    @error('deleteAccountForm.confirmation')
                    <x-ui.field.error>{{ $message }}</x-ui.field.error>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2">
                    <x-ui.button variant="outline" :label="__('app/settings/account/security.cancel')" @click="confirming = false"/>
                    <x-ui.button variant="destructive" :label="__('app/settings/account/security.delete_account_button')" wire:click="deleteAccount"/>
                </div>
            </div>
        </div>
    </div>
</div>
