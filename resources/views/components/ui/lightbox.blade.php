@blaze()

@props([
    'image' => 'lightboxImage', // The image or null when closed.
    'label' => 'Close',
])

<div
    x-show="{{ $image }}"
    x-cloak
    x-transition.opacity
    @keydown.escape.window="{{ $image }} = null"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
    @click="{{ $image }} = null"
>
    <img :src="{{ $image }}" alt="" class="max-h-[85vh] max-w-full rounded-lg object-contain shadow-lg" @click.stop>

    <button
        type="button"
        @click="{{ $image }} = null"
        class="extend-touch-target absolute top-4 right-4 flex size-9 items-center justify-center rounded-full bg-popover text-foreground shadow-xs"
    >
        <x-ui.icon.cancel-01 size="sm"/>
        <span class="sr-only">{{ $label }}</span>
    </button>
</div>
