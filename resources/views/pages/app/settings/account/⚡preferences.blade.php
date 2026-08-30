<?php

use App\Livewire\Traits\SwitchesLocale;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')]
class extends Component
{
    use SwitchesLocale;
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/account/preferences.breadcrumb_settings') => null,
            __('app/settings/account/preferences.breadcrumb_preferences') => null,
        ]"/>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
        <header class="flex flex-col gap-1 border-b border-border px-4 pb-6">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/account/preferences.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/account/preferences.description') }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/account/preferences.language_label')"
                :description="__('app/settings/account/preferences.language_description')"
            >
                <x-ui.select wire:change="setLocale($event.target.value)" class="w-full lg:w-64">
                    @foreach (config('app.available_locales') as $code => $label)
                        <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $label }}</option>
                    @endforeach
                </x-ui.select>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/account/preferences.theme_label')"
                :description="__('app/settings/account/preferences.theme_description')"
            >
                <x-ui.select
                    x-data
                    x-cloak
                    x-model="$store.theme.current"
                    x-on:change="$store.theme.set($event.target.value)"
                    class="w-full lg:w-64"
                >
                    <option value="system">{{ __('app/settings/account/preferences.theme_system') }}</option>
                    <option value="light">{{ __('app/settings/account/preferences.theme_light') }}</option>
                    <option value="dark">{{ __('app/settings/account/preferences.theme_dark') }}</option>
                </x-ui.select>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>
    </div>
</div>
