@props([
    'size' => 'default',
    'strokeWidth' => '2',
])

@php
    $strokeWidth ??= '2';

    // Size mapping
    $sizeClasses = match($size) {
        'xs' => 'size-3',
        'sm' => 'size-4',
        'default','lg' => 'size-5',
    };
@endphp

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    class="{{ $sizeClasses }} {{ $attributes->get('class') }}"
    aria-hidden="true"
    {{ $attributes->except(['class']) }}
>
    {{ $slot }}
</svg>
