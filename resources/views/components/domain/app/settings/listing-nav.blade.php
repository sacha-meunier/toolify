@props([
    'prevHref' => null,
    'prevLabel' => null,
    'nextHref' => null,
    'nextLabel' => null,
])

<div {{ $attributes->class('sticky bottom-0 z-10 flex h-14 shrink-0 items-center bg-background') }}>
    <div class="mx-auto flex w-full max-w-4xl items-center justify-between px-14">
        @if ($prevHref)
            <a href="{{ $prevHref }}" wire:navigate class="flex items-center gap-1 text-sm font-medium text-foreground hover:text-primary">
                <x-ui.icon.arrow-left-01 size="xs"/>
                {{ $prevLabel }}
            </a>
        @else
            <span class="flex items-center gap-1 text-sm text-muted-foreground/50">
                <x-ui.icon.arrow-left-01 size="xs"/>
                Previous
            </span>
        @endif

        @if ($nextHref)
            <a href="{{ $nextHref }}" wire:navigate class="flex items-center gap-1 text-sm font-medium text-foreground hover:text-primary">
                {{ $nextLabel }}
                <x-ui.icon.arrow-right-01 size="xs"/>
            </a>
        @else
            <span class="flex items-center gap-1 text-sm text-muted-foreground/50">
                Next
                <x-ui.icon.arrow-right-01 size="xs"/>
            </span>
        @endif
    </div>
</div>
