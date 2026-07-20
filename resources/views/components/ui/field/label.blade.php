@blaze()

@props([
    'content' => null, // content used when don‘t want slot and closing tag
])

<label {{ $attributes->class('font-medium text-sm leading-5 text-foreground') }}>
    {{ $content }}
    {{ $slot }}
</label>
