@blaze()

@props([
    'hasPreview' => false,
    'hasPersisted' => false,
    'previewModel' => null,
    'deleteAction' => null,
    'deleteConfirm' => null,
    'changeLabel',
    'removeLabel' => null,
])

<div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
    <div
        role="button"
        tabindex="0"
        aria-haspopup="true"
        :aria-expanded="open"
        @click="open = ! open"
        @keydown.enter="open = ! open"
        {{ $attributes->class('group relative flex items-center justify-center overflow-clip rounded-full') }}
    >
        {{ $slot }}

        <div class="absolute inset-0 flex items-center justify-center rounded-full bg-black/0 transition-colors group-hover:bg-black/50">
            <x-ui.icon.pen-01 size="xs" stroke-width="3" class="text-white opacity-0 transition-opacity group-hover:opacity-100"/>
        </div>
    </div>

    {{ $input }}

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute right-0 z-10 mt-1.5 w-44 overflow-clip rounded-md border border-border bg-popover py-1 shadow-xs"
    >
        <button
            type="button"
            @click="open = false; $refs.pickerInput.click()"
            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
        >
            <x-ui.icon.pen-01 size="sm" class="shrink-0 text-muted-foreground"/>
            <span class="truncate">{{ $changeLabel }}</span>
        </button>

        @if ($hasPreview || $hasPersisted)
            <button
                type="button"
                @click="open = false"
                @if ($hasPreview)
                    wire:click="$set('{{ $previewModel }}', null)"
                @else
                    wire:click="{{ $deleteAction }}"
                    @if ($deleteConfirm)
                        wire:confirm="{{ $deleteConfirm }}"
                    @endif
                @endif
                class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-destructive hover:bg-muted"
            >
                <x-ui.icon.delete-02 size="sm" class="shrink-0"/>
                <span class="truncate">{{ $removeLabel }}</span>
            </button>
        @endif
    </div>
</div>
