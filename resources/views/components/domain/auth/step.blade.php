@blaze()

@props([
    'title' => null,
])

<div {{ $attributes->class('flex flex-col items-center w-full') }}>
    <div class="flex flex-col gap-7 items-center shrink-0">
        <p class="font-semibold text-lg text-center text-foreground">{{ $title }}</p>
    </div>

    <div class="flex flex-col gap-4 items-start pt-6 w-full">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="flex flex-col gap-2 items-center justify-center pt-6 shrink-0">
            {{ $footer }}
        </div>
    @endisset
</div>
