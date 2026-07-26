@props([
    'icon' => null,
    'badge' => null,
    'disabled' => false,
])

<x-ui.button
    variant="ghost"
    size="lg"
    :href="$disabled ? null : $attributes->get('href')"
    :icon="$icon"
    :disabled="$disabled"
    wire:navigate
    {{ $attributes->except('href')->class('w-full justify-start gap-2 rounded-md px-2 font-normal text-sidebar-foreground hover:bg-sidebar-accent') }}
>
    <span class="flex-1 truncate text-left">{{ $slot }}</span>

    @if ($badge)
        <span class="text-xs text-sidebar-foreground">{{ $badge }}</span>
    @endif
</x-ui.button>
