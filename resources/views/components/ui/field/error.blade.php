@blaze()

@props([
    'content' => null, // content used when don‘t want slot and closing tag
])

<p
    x-data
    x-transition:enter="transition ease-out-strong duration-150"
    x-transition:enter-start="opacity-0 -translate-y-1"
    x-transition:enter-end="opacity-100 translate-y-0"
    {{ $attributes->class('text-sm leading-5 text-destructive') }}
>
    {{ $content }}
    {{ $slot }}
</p>
