@props([
    'icon' => null,
    'badge' => null,
    'disabled' => false,
])

@php
    $href = $attributes->get('href');
    $active = $href && $href !== '#' && url()->current() === $href;
@endphp

<x-ui.button
    variant="nav"
    size="lg"
    :href="$disabled ? null : $href"
    :icon="$icon"
    icon-stroke-width="1.5"
    :disabled="$disabled"
    wire:navigate
    {{ $attributes->except('href')->class([
        'w-full justify-start gap-2 rounded-md px-2 font-normal',
        'text-sidebar-foreground hover:bg-sidebar-accent/45 hover:text-sidebar-accent-foreground' => ! $active,
        'bg-sidebar-accent font-medium text-sidebar-accent-foreground' => $active,
    ]) }}
>
    <span class="flex-1 truncate text-left">{{ $slot }}</span>

    @if ($badge)
        <span class="text-xs text-sidebar-foreground">{{ $badge }}</span>
    @endif
</x-ui.button>
