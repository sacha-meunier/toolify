@blaze()

@props([
    'name' => null,
    'id' => null,
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => 50,
])

<input
    type="range"
    name="{{ $name }}"
    id="{{ $id }}"
    min="{{ $min }}"
    max="{{ $max }}"
    step="{{ $step }}"
    value="{{ $value }}"
    {{ $attributes->class('h-1.5 w-full cursor-pointer appearance-none rounded-full bg-muted accent-primary') }}
>
