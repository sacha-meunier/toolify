@props([
    'items', // ordered ['Label' => route URL or null, ...] — the last item always renders as the current page
])

<nav {{ $attributes->class('flex min-w-0 items-center gap-1.5') }}>
    @foreach ($items as $label => $url)
        @if (! $loop->last)
            @if ($url)
                <a href="{{ $url }}" wire:navigate class="truncate text-sm text-muted-foreground hover:text-foreground">{{ $label }}</a>
            @else
                <span class="truncate text-sm text-muted-foreground">{{ $label }}</span>
            @endif

            <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 text-muted-foreground"/>
        @else
            <span class="truncate text-sm font-medium text-foreground">{{ $label }}</span>
        @endif
    @endforeach
</nav>
