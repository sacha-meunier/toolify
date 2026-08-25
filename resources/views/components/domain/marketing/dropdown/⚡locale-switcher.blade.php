<?php

use App\Livewire\Traits\SwitchesLocale;
use Livewire\Component;

new class extends Component
{
    use SwitchesLocale;
};
?>

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <x-ui.button
        variant="ghost"
        size="sm"
        icon:trailing="arrow-down-01"
        class="gap-1.5 px-2.5 font-medium text-sidebar-foreground uppercase"
        @click="open = !open"
        :aria-label="__('app/components/dropdown/locale-switcher.switch_language')"
    >
        {{ app()->getLocale() }}
    </x-ui.button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute right-0 z-20 mt-1.5 w-40 overflow-visible rounded-md border border-border bg-background py-1 shadow-xs"
    >
        @foreach (config('app.available_locales') as $code => $label)
            <button
                type="button"
                wire:click="setLocale('{{ $code }}')"
                class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
            >
                <x-ui.icon.checkmark-circle-02
                    size="sm"
                    class="shrink-0 {{ app()->getLocale() === $code ? 'text-primary' : 'text-transparent' }}"
                />
                <span class="w-6 shrink-0 font-medium text-muted-foreground uppercase">{{ $code }}</span>
                <span class="truncate">{{ $label }}</span>
            </button>
        @endforeach
    </div>
</div>
