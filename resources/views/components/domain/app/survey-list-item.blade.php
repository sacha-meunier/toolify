@props([
    'survey',
])

@php
    $toolsCount = \App\Models\Tool::matching($survey->query ?? '', $survey->filters)->count();
@endphp

<div class="flex flex-wrap items-center gap-x-3 gap-y-2 px-4 py-3.5 hover:bg-muted lg:px-6">
    <a href="{{ route('surveys.show', $survey) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-2">
        <x-ui.icon.search-01 size="sm" class="shrink-0 text-muted-foreground"/>
        <p class="truncate text-sm font-medium text-foreground">"{{ $survey->name }}"</p>
    </a>

    <div class="flex shrink-0 items-center gap-3 pl-7 lg:pl-0">
        <p class="shrink-0 text-sm text-muted-foreground">{{ trans_choice('app/components/survey-list-item.tools_count', $toolsCount, ['count' => $toolsCount]) }}</p>
        <p class="w-24 shrink-0 text-right text-sm text-muted-foreground">{{ $survey->last_visited_at?->diffForHumans() ?? __('app/components/survey-list-item.never_visited') }}</p>

        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
            <x-ui.button variant="ghost" size="icon-sm" icon="more-horizontal-square-01" @click="open = !open"/>

            <div
                x-show="open"
                x-cloak
                x-transition
                class="absolute right-0 z-10 mt-1.5 w-44 overflow-clip rounded-md border border-border bg-background py-1 shadow-xs"
            >
                <button
                    type="button"
                    wire:click="openSurveyForm({{ $survey->id }})"
                    @click="open = false"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                >
                    <x-ui.icon.pen-01 size="sm" class="shrink-0"/>
                    <span>{{ __('app/components/survey-list-item.edit') }}</span>
                </button>

                <button
                    type="button"
                    wire:click="duplicate({{ $survey->id }})"
                    @click="open = false"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                >
                    <x-ui.icon.copy-01 size="sm" class="shrink-0"/>
                    <span>{{ __('app/components/survey-list-item.duplicate') }}</span>
                </button>

                <div class="my-1 h-px bg-border"></div>

                <button
                    type="button"
                    wire:click="delete({{ $survey->id }})"
                    wire:confirm="{{ __('app/components/survey-list-item.confirm_delete') }}"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-destructive hover:bg-muted"
                >
                    <x-ui.icon.delete-02 size="sm" class="shrink-0"/>
                    <span>{{ __('app/components/survey-list-item.delete') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
