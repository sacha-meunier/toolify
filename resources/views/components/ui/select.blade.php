@blaze()

@props([
    'name' => null,
    'id' => null,
    'disabled' => false,
])

@php
    // Binding directives (wire:model, wire:change, x-model, ...) must land on the actual
    // <select>, not the wrapping container, otherwise the directives don't work.
    $selectAttributes = $attributes->whereStartsWith(['wire:', 'x-model']);
    $containerAttributes = $attributes->whereDoesntStartWith(['wire:', 'x-model']);
@endphp

<div {{ $containerAttributes->class('relative flex h-8 w-full items-center rounded-lg border border-input bg-secondary') }}>
    <select
        {{ $selectAttributes }}
        name="{{ $name }}"
        id="{{ $id }}"
        @if($disabled) disabled @endif
        class="w-full flex-1 appearance-none bg-transparent px-2.5 py-1 pr-8 text-sm leading-5 text-foreground outline-none disabled:cursor-not-allowed disabled:opacity-50"
    >
        {{ $slot }}
    </select>

    <x-ui.icon.arrow-down-01 size="xs" class="pointer-events-none absolute inset-y-0 right-2.5 my-auto text-muted-foreground"/>
</div>
