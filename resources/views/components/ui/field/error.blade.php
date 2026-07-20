@blaze()

@props([
    'content' => null, // content used when don‘t want slot and closing tag
])

<p {{ $attributes->class('text-sm leading-5 text-destructive') }}>
    {{ $content }}
    {{ $slot }}
</p>
