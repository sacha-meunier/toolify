@blaze()

@props([
    'name' => null,
    'id' => null,
    'checked' => false,
    'disabled' => false,
])

<label {{ $attributes->class('relative inline-flex h-5 w-9 shrink-0 cursor-pointer items-center') }}>
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $id }}"
        @if($checked) checked @endif
        @if($disabled) disabled @endif
        class="peer sr-only"
    >
    <span class="h-5 w-9 rounded-full bg-muted transition-colors peer-checked:bg-primary peer-disabled:cursor-not-allowed peer-disabled:opacity-50"></span>
    <span class="absolute left-0.5 size-4 rounded-full bg-background shadow-xs transition-transform peer-checked:translate-x-4"></span>
</label>
