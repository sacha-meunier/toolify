@blaze()

@props(['strokeWidth' => null])

{{-- Credit: Hugeicons (https://hugeicons.com) --}}

<x-ui.icon {{ $attributes }} :stroke-width="$strokeWidth">
    <path d="M18 6L6 18M6 6L18 18" />
</x-ui.icon>
