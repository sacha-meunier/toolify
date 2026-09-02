@blaze()

@props([
    'show' => null, // Alpine/wire expression evaluating if the modal is open, e.g. "$wire.surveyFormOpen".
    'close' => null, // Livewire method called to close the modal, e.g. "closeSurveyForm".
])

<div
    x-data
    x-show="{{ $show }}"
    x-cloak
    x-transition:enter="transition-opacity ease-out-strong duration-150"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
>
    <div
        x-show="{{ $show }}"
        x-transition:enter="transition ease-out-strong duration-150"
        x-transition:enter-start="opacity-0 scale-(--scale-fade-initial)"
        x-transition:enter-end="opacity-100 scale-(--scale-fade-final)"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-(--scale-fade-final)"
        x-transition:leave-end="opacity-0 scale-(--scale-fade-initial)"
        @click.outside="$wire.{{ $close }}()"
        @keydown.escape.window="$wire.{{ $close }}()"
        {{ $attributes->class('flex max-h-[90vh] w-full flex-col gap-5 overflow-y-auto rounded-lg border border-border bg-popover p-6 shadow-lg') }}
    >
        @isset($header)
            <header>
                {{ $header }}
            </header>
        @endisset

        {{ $slot }}
    </div>
</div>
