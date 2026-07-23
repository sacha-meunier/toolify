@blaze()

@props([
    'author' => null,
    'quote' => null,
])

<figure {{ $attributes->class('rounded-xl border border-border bg-background p-6 shadow-xs') }}>
    <blockquote class="text-lg text-foreground">&ldquo;{{ $quote }}&rdquo;</blockquote>
    <figcaption class="mt-4 text-sm text-muted-foreground">&mdash; {{ $author }}</figcaption>
</figure>
