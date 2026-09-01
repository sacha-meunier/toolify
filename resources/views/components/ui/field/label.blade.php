@blaze()

@props([
    'content' => null, // content used when don‘t want slot and closing tag
    'required' => false,
])

<label {{ $attributes->class('font-medium text-sm leading-5 text-foreground') }}>
    {{ $content }}
    {{ $slot }}
    @if ($required)
        <span class="text-destructive" aria-hidden="true">*</span>
        <span class="sr-only">{{ __('components/ui/field.required') }}</span>
    @endif
</label>
