@blaze()

@props([
    'type' => 'text', // text, email, file, password
    'name' => null,
    'id' => null,
    'placeholder' => null,
    'value' => null,
    'autocomplete' => 'off',
    'spellcheck' => 'false',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
])

@php
    $baseClasses = 'w-full relative block border border-input bg-secondary flex gap-1 h-8 items-center rounded-lg focus-within:border-ring focus-within:ring-3 focus-within:ring-ring/50';

    $inputClasses = 'w-full flex-1 px-2.5 py-1 rounded-lg text-sm leading-5 bg-transparent outline-none text-foreground placeholder-muted-foreground disabled:opacity-50 disabled:cursor-not-allowed';

    $containerClasses = "$baseClasses";

    // Binding directives (wire:model, x-model, ...) must land on the actual <input>,
    // not the wrapping container, otherwise the directives don't work.
    $inputAttributes = $attributes->whereStartsWith(['wire:', 'x-model']);
    $containerAttributes = $attributes->whereDoesntStartWith(['wire:', 'x-model']);
@endphp

@switch($type)
    @case('text')
    @case('email')
    @case('file')
        <div {{ $containerAttributes->class($containerClasses) }}>
            <input
                {{ $inputAttributes }}
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $id }}"
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                value="{{ is_string(old($name)) ? old($name) : ($value ?? '') }}"
                class="{{ $inputClasses }}"
                autocomplete="{{ $autocomplete }}"
                spellcheck="{{ $spellcheck }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            >
        </div>
        @break
    @case('password')
        <div {{ $containerAttributes->class($containerClasses) }} x-data="{ show: false }">
            <input
                {{ $inputAttributes }}
                x-bind:type="show ? 'text' : 'password'"
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $id }}"
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                value="{{ is_string(old($name)) ? old($name) : ($value ?? '') }}"
                class="{{ $inputClasses }}"
                autocomplete="{{ $autocomplete }}"
                spellcheck="{{ $spellcheck }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            >
            <button
                type="button"
                @click="show = !show"
                aria-label="{{ __('components/ui/input.show_password') }}"
                x-bind:aria-label="show ? '{{ __('components/ui/input.hide_password') }}' : '{{ __('components/ui/input.show_password') }}'"
                x-bind:aria-pressed="show"
                class="text-muted-foreground hover:text-foreground focus-visible:ring-ring/50 absolute inset-y-0 inset-e-0 flex items-center rounded-md px-3 outline-none transition-colors focus-visible:ring-3"
            >
                {{-- eye (password hidden) --}}
                <x-ui.icon.eye x-show="!show" size="sm"/>
                {{-- eye-off (password visible) --}}
                <x-ui.icon.eye-off x-show="show" size="sm"/>
            </button>
        </div>
        @break
@endswitch
