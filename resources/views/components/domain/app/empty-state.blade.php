@props([
    'icon' => 'inbox',
    'title' => null,
    'description' => null,
])

<div class="flex flex-col items-center gap-3 px-4 py-12 text-center">
    <x-dynamic-component :component="'ui.icon.'.$icon" class="size-8 text-muted-foreground"/>

    <div class="flex flex-col gap-1">
        <p class="text-sm font-medium text-foreground">{{ $title }}</p>
        @if ($description)
            <p class="max-w-sm text-sm text-muted-foreground">{{ $description }}</p>
        @endif
    </div>

    {{ $slot ?? '' }}
</div>
