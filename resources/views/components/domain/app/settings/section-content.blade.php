@props([
    'label' => null,
    'description' => null,
    'icon' => null,
    'chevron' => false,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" wire:navigate @endif
    {{ $attributes->class([
        'flex min-h-[30px] items-center gap-3 px-4 py-4',
        'hover:bg-muted/50' => $href,
    ]) }}
>
    @if ($icon)
        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground">
            <x-dynamic-component :component="'ui.icon.'.$icon" size="sm"/>
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-0.5">
        @if ($label)
            <p class="text-xs font-semibold text-foreground">{{ $label }}</p>
        @endif

        @if ($description)
            <p class="text-xs text-muted-foreground">{{ $description }}</p>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="flex shrink-0 items-center">
            {{ $slot }}
        </div>
    @endif

    @if ($chevron)
        <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 text-muted-foreground"/>
    @endif
</{{ $tag }}>
