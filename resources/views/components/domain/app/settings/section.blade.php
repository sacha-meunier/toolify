@props([
    'label' => null,
    'description' => null,
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

    <div class="flex w-full flex-col divide-y divide-border overflow-clip rounded-xl border border-border bg-card shadow-xs">
        {{ $slot }}
    </div>
</section>
