@blaze()

@props([
    'title' => null,
    'description' => null,
    'reverse' => false,
])

<section {{ $attributes->class('mx-auto max-w-6xl px-6 py-24 lg:px-9') }}>
    <div class="grid items-center gap-12 lg:grid-cols-2">
        <div class="{{ $reverse ? 'lg:order-2' : '' }}">
            <h2 class="text-3xl font-semibold tracking-tight text-foreground">{{ $title }}</h2>
            <p class="mt-4 text-muted-foreground">{{ $description }}</p>
        </div>

        <div class="{{ $reverse ? 'lg:order-1' : '' }} w-full overflow-clip rounded-xl border border-border bg-muted shadow-xs">
            {{ $media ?? '' }}
        </div>
    </div>
</section>
