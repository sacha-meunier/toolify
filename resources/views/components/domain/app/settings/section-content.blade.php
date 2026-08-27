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
        'flex min-h-[30px] flex-col gap-3 px-4 py-4 lg:flex-row lg:items-center',
        'hover:bg-muted/50' => $href,
    ]) }}
>
    <div class="flex min-w-0 flex-1 items-center gap-3">
        @if ($icon)
            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-muted text-muted-foreground">
                <x-dynamic-component :component="'ui.icon.'.$icon" size="sm"/>
            </div>
        @endif

        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
            @if ($label)
                <p class="text-xs font-semibold text-foreground">{{ $label }}</p>
            @endif

            @if ($description)
                <p class="text-xs text-muted-foreground">{{ $description }}</p>
            @endif
        </div>
    </div>

    @if (! $slot->isEmpty() || $chevron)
        <div class="flex w-full shrink-0 items-center justify-end gap-3 pl-11 lg:w-auto lg:pl-0">
            {{ $slot }}

            @if ($chevron)
                <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 text-muted-foreground"/>
            @endif
        </div>
    @endif
</{{ $tag }}>
