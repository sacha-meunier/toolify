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
        <div class="relative" x-data="{ open: false, position: { top: 0, left: 0 } }" @click.outside="open = false">
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

            <x-ui.dropdown-panel
                x-show="open"
                x-bind:style="`position: fixed; top: ${position.top}px; left: ${position.left}px;`"
                class="z-50 max-h-[70vh] w-64 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-md border border-border bg-popover p-1 shadow-md lg:max-h-none lg:overflow-visible"
            >
                @foreach ([
                    ['group' => 'pricing', 'label' => __('app/components/topbar/search.filter_group_price'), 'cases' => \App\Enums\Pricing::cases()],
                    ['group' => 'platforms', 'label' => __('app/components/topbar/search.filter_group_platform'), 'cases' => \App\Enums\Platform::cases()],
                    ['group' => 'categories', 'label' => __('app/components/topbar/search.filter_group_category'), 'cases' => \App\Enums\Category::cases()],
                ] as $type)
                    <div class="relative" x-data="{ expanded: false }" @mouseenter="if (window.innerWidth >= 1024) expanded = true" @mouseleave="if (window.innerWidth >= 1024) expanded = false">
                        <button
                            type="button"
                            @click="expanded = ! expanded"
                            class="flex w-full items-center justify-between rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                        >
                            <span class="truncate">
                                {{ $type['label'] }}
                                @if (count($filters[$type['group']]) > 0)
                                    <span class="text-muted-foreground">({{ count($filters[$type['group']]) }})</span>
                                @endif
                            </span>
                            <x-ui.icon.arrow-right-01
                                size="xs"
                                class="shrink-0 rotate-90 opacity-50 transition-transform lg:rotate-180"
                                x-bind:class="{ '-rotate-90': expanded }"
                            />
                        </button>

                        <div
                            x-show="expanded"
                            x-cloak
                            x-transition:enter="transition ease-out-strong duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="origin-top bg-muted/30 py-1 lg:absolute lg:top-0 lg:right-full lg:z-30 lg:mr-1 lg:w-56 lg:origin-top-right lg:overflow-hidden lg:rounded-md lg:border lg:border-border lg:bg-popover lg:p-1 lg:shadow-xs"
                        >
                            @foreach ($type['cases'] as $case)
                                <button
                                    type="button"
                                    wire:click="toggleFilter('{{ $type['group'] }}', '{{ $case->value }}')"
                                    class="flex w-full items-center gap-2 rounded py-2 pr-3 pl-6 text-left text-sm text-foreground hover:bg-muted"
                                >
                                    <x-ui.icon.checkmark-circle-02
                                        size="sm"
                                        class="shrink-0 transition-colors duration-150 ease-out {{ in_array($case->value, $filters[$type['group']], true) ? 'text-primary' : 'text-transparent' }}"
                                    />
                                    <span class="truncate">{{ $case->label() }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if ($activeFilterCount > 0)
                    <div class="-mx-1 my-1 h-px bg-border"></div>

                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="flex w-full items-center rounded px-3 py-2 text-left text-sm text-muted-foreground hover:bg-muted hover:text-foreground"
                    >
                        {{ __('app/components/topbar/search.clear_all_filters') }}
                    </button>
                @endif
            </x-ui.dropdown-panel>
        </div>
    </x-slot:actions>
</x-domain.app.topbar>
