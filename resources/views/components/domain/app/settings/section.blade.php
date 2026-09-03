@props([
    'label' => null,
    'description' => null,
    'card' => true, // Wrap the slot in a bordered card.
])

<section {{ $attributes->class('flex w-full flex-col gap-2') }}>
    @if ($label || $description)
        <header class="flex flex-col px-4">
            @if ($label)
                <p class="text-base font-semibold text-foreground">{{ $label }}</p>
            @endif

            @if ($description)
                <p class="text-sm text-muted-foreground">{{ $description }}</p>
            @endif
        </header>
    @endif

    @if ($card)
        <div class="flex w-full flex-col divide-y divide-border rounded-xl border border-border bg-card shadow-xs [&>:first-child]:rounded-t-xl [&>:last-child]:rounded-b-xl">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</section>
