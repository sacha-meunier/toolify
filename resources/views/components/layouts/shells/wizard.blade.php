@props([
    'backHref' => null,
    'title' => null,
])

@php
    $backHref ??= session('workspace_wizard_back_href', route('discovery'));
@endphp

<x-layouts.root :title="$title">
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <div class="p-4">
            <x-ui.button
                variant="ghost"
                size="sm"
                icon="arrow-left-01"
                :href="$backHref"
                wire:navigate
                class="gap-1.5 font-normal text-muted-foreground"
            >
                {{ __('components/layouts/shells/wizard.back_button', ['app' => config('app.name')]) }}
            </x-ui.button>
        </div>

        <div class="flex flex-1 justify-center px-4">
            <div {{ $attributes->merge(['class' => 'flex w-full max-w-2xs flex-col gap-7 mt-[max(0px,-240px+50svh)]']) }}>
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.root>
