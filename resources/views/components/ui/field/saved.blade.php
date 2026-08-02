@blaze()

@props([
    'event',
])

<span
    x-data="{ show: false }"
    x-on:{{ $event }}.window="show = true; clearTimeout($el._timeout); $el._timeout = setTimeout(() => show = false, 2000)"
    x-show="show"
    x-cloak
    x-transition.opacity
    {{ $attributes->class('flex items-center gap-1 text-xs text-muted-foreground') }}
>
    <x-ui.icon.checkmark-circle-02 size="xs"/>
    Saved
</span>
