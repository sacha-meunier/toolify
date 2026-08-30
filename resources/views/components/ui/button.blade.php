@blaze()

@props([
    'variant' => 'outline', // primary, secondary, destructive, outline, ghost, nav, link
    'type' => 'button', // button, a
    'href' => null,
    'size' => 'default', // default, xs, sm, lg, icon, icon-xs, icon-sm, icon-lg
    'label' => null, // label used when don‘t want slot and closing tag
    'icon' => null,
])

@php
    $baseClasses = "relative inline-flex items-center justify-center font-medium whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none &_svg]:pointer-events-none extend-touch-target shrink-0";

    $sizeClasses = match ($size) {
        'xs' => 'h-6 text-xs rounded-md gap-1 px-2',
        'sm' => 'h-7 text-xs rounded-md gap-1 px-2.5',
        'default' => 'h-8 text-sm rounded-lg gap-1.5 px-2.5',
        'lg' => 'h-9 text-sm rounded-lg gap-1.5 px-2.5',
        'icon-xs' => 'size-6 rounded-lg',
        'icon-sm' => 'size-7 rounded-lg',
        'icon' => 'size-8 rounded-lg',
        'icon-lg' => 'size-9 rounded-lg',
    };

    $variantClasses = match ($variant) {
        'primary' => 'bg-primary text-primary-foreground hover:bg-primary/80 focus-visible:ring-3 focus-visible:ring-ring/50',
        'secondary' => 'bg-secondary text-secondary-foreground hover:bg-accent focus-visible:ring-3 focus-visible:ring-ring/50',
        'destructive' => 'bg-destructive/10 text-destructive hover:bg-destructive/20 focus-visible:ring-3 focus-visible:ring-destructive/20',
        'outline' => 'bg-secondary text-foreground border border-border hover:bg-accent focus-visible:ring-3 focus-visible:ring-ring/50',
        'ghost' => 'text-foreground hover:bg-accent focus-visible:ring-3 focus-visible:ring-ring/50',
        'nav' => 'focus-visible:ring-3 focus-visible:ring-ring/50',
        'link' => 'text-primary underline-offset-4 hover:underline',
    };

    $classes = "$baseClasses $sizeClasses $variantClasses";

    $iconSize = match ($size) {
        'icon-xs' => 'xs',
        'icon-sm' => 'sm',
        'icon' => 'default',
        'icon-lg' => 'lg',
        default => $size,
    };

    $tag = $href ? 'a' : 'button';

    $trailingIcon = $attributes->get('icon:trailing');
@endphp

@switch($tag)
    @case('a')
        <a href="{{ $href }}" {{ $attributes->except('icon:trailing')->class($classes) }}>
            @if ($icon)
                <x-dynamic-component :size="$iconSize" :component="'ui.icon.'.$icon"/>
            @endif
            {{ $label }}
            {{ $slot }}
            @if ($trailingIcon)
                <x-dynamic-component :size="$iconSize" :component="'ui.icon.'.$trailingIcon"/>
            @endif
        </a>
        @break

    @case('button')
        <button type="{{ $type }}" {{ $attributes->except('icon:trailing')->class($classes) }}>
            @if ($icon)
                <x-dynamic-component :size="$iconSize" :component="'ui.icon.'.$icon"/>
            @endif
            {{ $label }}
            {{ $slot }}
            @if ($trailingIcon)
                <x-dynamic-component :size="$iconSize" :component="'ui.icon.'.$trailingIcon"/>
            @endif
        </button>
        @break
@endswitch
