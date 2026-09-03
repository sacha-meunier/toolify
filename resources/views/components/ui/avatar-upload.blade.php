@blaze()

@props([
    'hasPreview' => false,
    'hasPersisted' => false,
    'previewModel' => null,
    'deleteAction' => null,
    'chooseLabel',
    'removeLabel' => null,
])

<div class="flex items-center gap-3">
    <div {{ $attributes->class('group relative flex shrink-0 items-center justify-center overflow-clip rounded-lg') }}>
        {{ $slot }}

        @if ($hasPreview || $hasPersisted)
            <button
                type="button"
                @if ($removeLabel)
                    aria-label="{{ $removeLabel }}"
                @endif
                @if ($hasPreview)
                    wire:click="$set('{{ $previewModel }}', null)"
                @else
                    wire:click="{{ $deleteAction }}"
                @endif
                class="absolute inset-0 flex items-center justify-center rounded-lg bg-black/0 opacity-0 transition-all group-hover:bg-black/50 group-hover:opacity-100"
            >
                <x-ui.icon.cancel-01 size="xs" stroke-width="3" class="text-white"/>
            </button>
        @endif
    </div>

    {{ $input }}

    <x-ui.button type="button" variant="outline" size="sm" :label="$chooseLabel" x-on:click="$refs.pickerInput.click()"/>
</div>
