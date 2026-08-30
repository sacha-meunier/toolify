@blaze()

@props([
    'show' => null, // Alpine/wire expression evaluating if the modal is open, e.g. "$wire.surveyFormOpen".
    'close' => null, // Livewire method called to close the modal, e.g. "closeSurveyForm".
])

<div x-data x-show="{{ $show }}" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div
        x-show="{{ $show }}"
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
