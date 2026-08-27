@props([
    'prevHref' => null,
    'prevLabel' => null,
    'nextHref' => null,
    'nextLabel' => null,
])

<div {{ $attributes->class('sticky bottom-0 z-10 flex h-14 shrink-0 items-center bg-background') }}>
    <div class="mx-auto flex w-full max-w-4xl items-center justify-between gap-3 px-4 lg:px-14">
        @if ($prevHref)
            <a href="{{ $prevHref }}" wire:navigate class="flex min-w-0 items-center gap-1 text-sm font-medium text-foreground hover:text-primary">
                <x-ui.icon.arrow-left-01 size="xs" class="shrink-0"/>
                <span class="min-w-0 truncate">{{ $prevLabel }}</span>
            </a>
        @else
            <span class="flex min-w-0 items-center gap-1 text-sm text-muted-foreground/50">
                <x-ui.icon.arrow-left-01 size="xs" class="shrink-0"/>
                <span class="min-w-0 truncate">{{ __('app/components/settings/listing-nav.previous') }}</span>
            </span>
        @endif

        @if ($nextHref)
            <a href="{{ $nextHref }}" wire:navigate class="flex min-w-0 items-center gap-1 text-sm font-medium text-foreground hover:text-primary">
                <span class="min-w-0 truncate">{{ $nextLabel }}</span>
                <x-ui.icon.arrow-right-01 size="xs" class="shrink-0"/>
            </a>
        @else
            <span class="flex min-w-0 items-center gap-1 text-sm text-muted-foreground/50">
                <span class="min-w-0 truncate">{{ __('app/components/settings/listing-nav.next') }}</span>
                <x-ui.icon.arrow-right-01 size="xs" class="shrink-0"/>
            </span>
        @endif
    </div>
</div>
