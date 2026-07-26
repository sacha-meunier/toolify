@props([
    'label' => null,
])

<div {{ $attributes->class('flex w-full flex-col items-start p-2') }}>
    @if ($label)
        <div class="flex h-8 w-full shrink-0 items-center justify-between rounded-md px-2 text-sm font-medium text-sidebar-foreground">
            <span>{{ $label }}</span>

            {{-- TODO : Add collapsible behavior --}}
            {{--<x-ui.icon.arrow-down-01 size="sm" class="shrink-0 opacity-50"/>--}}
        </div>
    @endif

    <div class="flex w-full flex-col items-start">
        {{ $slot }}
    </div>
</div>
