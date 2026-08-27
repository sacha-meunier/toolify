@props([
    'name' => 'search',
    'placeholder' => null,
    'filters' => [
        'pricing' => [],
        'categories' => [],
        'platforms' => [],
    ],
])

@php
    $activeFilterCount = collect($filters)->flatten()->count();
    $placeholder ??= __('app/components/topbar/search.placeholder');
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
        <div class="relative" x-data="{ open: false, expandedGroup: null, position: { top: 0, left: 0 } }" @click.outside="open = false">
            <x-ui.button
                variant="outline"
                icon="filter"
                @click="
                    const rect = $el.getBoundingClientRect();
                    position = { top: rect.bottom + window.scrollY + 6, left: Math.min(rect.left + window.scrollX, window.innerWidth - 272) };
                    open = ! open;
                "
            >
                {{ __('app/components/topbar/search.filters') }}
                @if ($activeFilterCount > 0)
                    <x-ui.badge class="bg-primary text-primary-foreground">{{ $activeFilterCount }}</x-ui.badge>
                @endif
            </x-ui.button>

            <div
                x-show="open"
                x-cloak
                x-transition
                x-bind:style="`position: fixed; top: ${position.top}px; left: ${position.left}px;`"
                class="z-50 max-h-[70vh] w-64 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-md border border-border bg-background py-1 shadow-md lg:max-h-none lg:overflow-visible"
            >
                @foreach ([
                    ['group' => 'pricing', 'label' => __('app/components/topbar/search.filter_group_price'), 'cases' => \App\Enums\Pricing::cases()],
                    ['group' => 'platforms', 'label' => __('app/components/topbar/search.filter_group_platform'), 'cases' => \App\Enums\Platform::cases()],
                    ['group' => 'categories', 'label' => __('app/components/topbar/search.filter_group_category'), 'cases' => \App\Enums\Category::cases()],
                ] as $type)
                    <div>
                        <button
                            type="button"
                            @click="expandedGroup = expandedGroup === '{{ $type['group'] }}' ? null : '{{ $type['group'] }}'"
                            class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                        >
                            <span class="truncate">
                                {{ $type['label'] }}
                                @if (count($filters[$type['group']]) > 0)
                                    <span class="text-muted-foreground">({{ count($filters[$type['group']]) }})</span>
                                @endif
                            </span>
                            <x-ui.icon.arrow-right-01
                                size="xs"
                                class="shrink-0 rotate-90 opacity-50 transition-transform"
                                x-bind:class="{ '-rotate-90': expandedGroup === '{{ $type['group'] }}' }"
                            />
                        </button>

                        <div x-show="expandedGroup === '{{ $type['group'] }}'" x-cloak class="bg-muted/30 py-1">
                            @foreach ($type['cases'] as $case)
                                <button
                                    type="button"
                                    wire:click="toggleFilter('{{ $type['group'] }}', '{{ $case->value }}')"
                                    class="flex w-full items-center gap-2 py-2 pr-3 pl-6 text-left text-sm text-foreground hover:bg-muted"
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
                        {{ __('app/components/topbar/search.clear_all_filters') }}
                    </button>
                @endif
            </div>
        </div>
    </x-slot:actions>
</x-domain.app.topbar>
