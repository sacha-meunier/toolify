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

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 border-b border-border px-4 pb-6">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/account/preferences.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/account/preferences.description') }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/account/preferences.default_homepage_label')"
                :description="__('app/settings/account/preferences.default_homepage_description')"
            >
                <div class="flex items-center gap-3">
                    <x-ui.badge>{{ __('app/settings/account/preferences.coming_soon') }}</x-ui.badge>
                    <x-ui.select disabled class="w-64">
                        <option>{{ __('app/settings/account/preferences.default_homepage_search') }}</option>
                        <option>{{ __('app/settings/account/preferences.default_homepage_discovery') }}</option>
                        <option>{{ __('app/settings/account/preferences.default_homepage_my_stack') }}</option>
                        <option>{{ __('app/settings/account/preferences.default_homepage_surveys') }}</option>
                    </x-ui.select>
                </div>
            </x-domain.app.settings.section-content>

            <x-domain.app.settings.section-content
                :label="__('app/settings/account/preferences.language_label')"
                :description="__('app/settings/account/preferences.language_description')"
            >
                <x-ui.select wire:change="setLocale($event.target.value)" class="w-64">
                    @foreach (config('app.available_locales') as $code => $label)
                        <option value="{{ $code }}" @selected(app()->getLocale() === $code)>{{ $label }}</option>
                    @endforeach
                </x-ui.select>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>
    </div>
</div>
