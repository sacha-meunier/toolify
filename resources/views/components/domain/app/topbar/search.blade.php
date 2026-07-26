@props([
    'name' => 'search',
    'placeholder' => 'Search tools, apps, and companies...',
])

<x-domain.app.topbar {{ $attributes->whereDoesntStartWith('wire:') }}>
    <x-ui.icon.search-01 size="sm" class="shrink-0 text-muted-foreground"/>

    <input
        type="text"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        autocomplete="off"
        {{ $attributes->whereStartsWith('wire:') }}
        class="w-full flex-1 bg-transparent text-sm text-foreground outline-none placeholder-muted-foreground"
    >

    <x-slot:actions>
        <x-ui.button variant="outline" icon="filter" label="Filters"/>
    </x-slot:actions>
</x-domain.app.topbar>
