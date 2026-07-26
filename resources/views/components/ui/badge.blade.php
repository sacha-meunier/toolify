@props([])

<span {{ $attributes->class('inline-flex shrink-0 items-center gap-1 rounded-full bg-secondary px-2 py-0.5 text-xs font-medium text-secondary-foreground') }}>
    {{ $slot }}
</span>
