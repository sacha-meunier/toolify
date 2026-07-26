@props([])

<div {{ $attributes->class('flex h-14 shrink-0 items-center justify-between gap-4 border-b border-border px-8') }}>
    <div class="flex min-w-0 flex-1 items-center gap-2">
        {{ $slot }}
    </div>

    @isset($actions)
        <div class="flex shrink-0 items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
