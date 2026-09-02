@blaze()

@props([
    'show' => 'confirming',
    'heading' => null,
    'description' => null,
    'cancelLabel' => 'Cancel',
])

<div x-show="{{ $show }}" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div
        x-show="{{ $show }}"
        @click.outside="{{ $show }} = false"
        @keydown.escape.window="{{ $show }} = false"
        class="flex w-full max-w-md flex-col gap-5 rounded-lg border border-border bg-popover p-6 shadow-lg"
    >
        <header class="flex flex-col gap-1">
            <h2 class="text-lg font-semibold text-foreground">{{ $heading }}</h2>

            @if ($description)
                <p class="text-sm text-muted-foreground">{{ $description }}</p>
            @endif
        </header>

        @isset($body)
            {{ $body }}
        @endisset

        <div class="flex items-center justify-end gap-2">
            <x-ui.button variant="outline" :label="$cancelLabel" @click="{{ $show }} = false"/>

            {{ $slot }}
        </div>
    </div>
</div>
