@props([
    'origin' => 'top-left', // top-left, top-right, top, bottom-left, bottom-right
])

@php
    $originClass = match ($origin) {
        'top-right' => 'origin-top-right',
        'top' => 'origin-top',
        'bottom-left' => 'origin-bottom-left',
        'bottom-right' => 'origin-bottom-right',
        default => 'origin-top-left',
    };
@endphp

<div
    x-cloak
    x-transition:enter="transition ease-out-strong duration-150"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    {{ $attributes->class([$originClass]) }}
>
    {{ $slot }}
</div>
