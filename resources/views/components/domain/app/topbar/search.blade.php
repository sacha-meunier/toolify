@props([
    'name' => 'search',
    'placeholder' => 'Search tools, apps, and companies...',
    'filters' => [
        'pricing' => [],
        'categories' => [],
        'platforms' => [],
    ],
])

@php
    $activeFilterCount = collect($filters)->flatten()->count();
@endphp

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
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <x-ui.button variant="outline" icon="filter" @click="open = !open">
                Filters
                @if ($activeFilterCount > 0)
                    <x-ui.badge class="bg-primary text-primary-foreground">{{ $activeFilterCount }}</x-ui.badge>
                @endif
            </x-ui.button>

            <div
                x-show="open"
                x-cloak
                x-transition
                class="absolute right-0 z-20 mt-1.5 w-56 overflow-visible rounded-md border border-border bg-background py-1 shadow-xs"
            >
                @foreach ([
                    ['group' => 'pricing', 'label' => 'Price', 'cases' => \App\Enums\Pricing::cases()],
                    ['group' => 'platforms', 'label' => 'Platform', 'cases' => \App\Enums\Platform::cases()],
                    ['group' => 'categories', 'label' => 'Category', 'cases' => \App\Enums\Category::cases()],
                ] as $type)
                    <div
                        class="relative"
                        x-data="{ hover: false, openLeft: false }"
                        @mouseenter="openLeft = ($el.getBoundingClientRect().right + 224) > window.innerWidth; hover = true"
                        @mouseleave="hover = false"
                    >
                        <button
                            type="button"
                            @click="openLeft = ($el.getBoundingClientRect().right + 224) > window.innerWidth; hover = !hover"
                            class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                        >
                            <span class="truncate">
                                {{ $type['label'] }}
                                @if (count($filters[$type['group']]) > 0)
                                    <span class="text-muted-foreground">({{ count($filters[$type['group']]) }})</span>
                                @endif
                            </span>
                            <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 opacity-50" x-bind:class="{ '-scale-x-100': openLeft }"/>
                        </button>

                        <div
                            x-show="hover"
                            x-cloak
                            x-transition
                            x-bind:class="openLeft ? 'right-full mr-1' : 'left-full ml-1'"
                            class="absolute top-0 max-h-96 w-56 overflow-y-auto rounded-md border border-border bg-background py-1 shadow-xs"
                        >
                            @foreach ($type['cases'] as $case)
                                <button
                                    type="button"
                                    wire:click="toggleFilter('{{ $type['group'] }}', '{{ $case->value }}')"
                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                                >
                                    <x-ui.icon.checkmark-circle-02
                                        size="sm"
                                        class="shrink-0 {{ in_array($case->value, $filters[$type['group']], true) ? 'text-primary' : 'text-transparent' }}"
                                    />
                                    <span class="truncate">{{ $case->label() }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if ($activeFilterCount > 0)
                    <div class="my-1 h-px bg-border"></div>

                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="flex w-full items-center px-3 py-2 text-left text-sm text-muted-foreground hover:bg-muted hover:text-foreground"
                    >
                        Clear all filters
                    </button>
                @endif
            </div>
        </div>
    </x-slot:actions>
</x-domain.app.topbar>
