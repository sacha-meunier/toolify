@blaze()

@props([
    'variant' => 'outline', // primary, secondary, destructive, outline, ghost
    'type' => 'button', // button, a
    'align' => 'center', // start, end, center
    'size' => 'default', // default, xs, md, lg
    'square' => null,
])

@php
    // Button should be a square if it has no text contents...
    $square ??= $slot->isEmpty();

    $baseClasses = "relative inline-flex items-center font-medium justify-center whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none extend-touch-target";

    $sizeClasses = match ($size) {
        'xs' => 'h-6 text-xs rounded-md gap-1' . ' ' . ($square ? 'w-6' : 'px-2'),
        'default' => 'h-8 text-sm rounded-lg gap-1.5' . ' ' . ($square ? 'w-8' : 'px-2.5'),
        'sm' => 'h-7 text-xs rounded-md gap-1' . ' ' . ($square ? 'w-7' : 'px-2.5'),
        'lg' => 'h-9 text-sm rounded-lg gap-1.5' . ' ' . ($square ? 'w-9' : 'px-2.5'),
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-primary text-primary-foreground hover:opacity-80 focus-visible:ring-3 focus-visible:ring-ring/50',
        'secondary' => 'bg-secondary text-secondary-foreground hover:opacity-70 focus-visible:ring-3 focus-visible:ring-ring/50',
        'destructive' => 'bg-destructive text-destructive-foreground hover:opacity-70 focus-visible:ring-3 focus-visible:ring-ring/50',
        'outline' => 'bg-background text-foreground border border-border hover:bg-muted focus-visible:ring-3 focus-visible:ring-ring/50',
        'ghost' => 'text-foreground hover:bg-muted focus-visible:ring-3 focus-visible:ring-ring/50',
    };

    $alignClasses = match ($align) {
        'start' => 'justify-start',
        'center' => 'justify-center',
        'end' => 'justify-end',
    };

    $classes = "$baseClasses $sizeClasses $variantClasses $alignClasses";

@endphp

@if ($type === 'a' || $attributes->has('href'))
    <a {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
