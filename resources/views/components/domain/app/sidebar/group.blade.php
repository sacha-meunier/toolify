@props([
    'label' => null,
])

<div {{ $attributes->class('flex w-full flex-col items-start p-2') }}>
    @if ($label)
        <div class="flex h-8 w-full shrink-0 items-center gap-1.5 rounded-md px-2 text-sm font-medium text-sidebar-foreground">
            <span class="shrink-0 truncate">{{ $label }}</span>

            @isset($actions)
                <div class="min-w-0 flex-1">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    <div class="flex w-full flex-col items-start gap-0.5">
        {{ $slot }}
    </div>
</div>
