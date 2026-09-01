<?php

use App\Livewire\Traits\ManagesSurveyForm;
use App\Models\Survey;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use ManagesSurveyForm;

    public const int MAX_RECENT_SEARCHES = 10;

    public const int RECENT_SEARCHES_RETENTION_DAYS = 14;

    public string $search = '';

    /**
     * Hydrated from the browser's localStorage on load via syncRecentSearches(),
     * since this history is per-device and shouldn't round-trip on every request.
     */
    public array $recentSearches = [];

    /**
     * @var array{pricing: array<string>, categories: array<string>, platforms: array<string>}
     */
    public array $filters = [
        'pricing' => [],
        'categories' => [],
        'platforms' => [],
    ];

    public function toggleFilter(string $group, string $value): void
    {
        $this->filters[$group] = in_array($value, $this->filters[$group], true)
            ? array_values(array_diff($this->filters[$group], [$value]))
            : [...$this->filters[$group], $value];
    }

    public function clearFilters(): void
    {
        $this->filters = [
            'pricing' => [],
            'categories' => [],
            'platforms' => [],
        ];
    }

    #[Computed]
    public function hasActiveFilters(): bool
    {
        return collect($this->filters)->flatten()->isNotEmpty();
    }

    /* Called on page load. */
    public function syncRecentSearches(array $searches): void
    {
        /* Counts the time till the due date. */
        $cutoff = now()->subDays(self::RECENT_SEARCHES_RETENTION_DAYS)->timestamp;

        $this->recentSearches = collect($searches)
            ->filter(fn (array $recentSearch) => ($recentSearch['savedAt'] ?? 0) >= $cutoff)
            ->take(self::MAX_RECENT_SEARCHES)
            ->values()
            ->all();
    }

    public function saveAllRecentSearchesAsSurveys(): void
    {
        foreach ($this->recentSearches as $recentSearch) {
            auth()->user()->surveys()->create([
                'name' => $recentSearch['query'],
                'query' => $recentSearch['query'],
                'filters' => ['pricing' => [], 'categories' => [], 'platforms' => []],
            ]);
        }
    }

    public function clearRecentSearches(): void
    {
        $this->recentSearches = [];

        /* Used to rewrite the localStorage. */
        $this->dispatch('recent-searches-changed', searches: $this->recentSearches);
    }

    public function updatingSearch(string $value): void
    {
        if ($value !== '' || $this->search === '') {
            return;
        }

        $this->recentSearches = collect($this->recentSearches)
            /* Remove existing search if already exists in history. */
            ->reject(fn (array $recentSearch) => $recentSearch['query'] === $this->search)
            /* Adds the new search on top of the list. */
            ->prepend(['query' => $this->search, 'filters' => [], 'savedAt' => now()->timestamp])
            ->take(self::MAX_RECENT_SEARCHES)
            ->all();

        $this->dispatch('recent-searches-changed', searches: $this->recentSearches);
    }

    #[Computed]
    public function tools(): Collection
    {
        return Tool::matching($this->search, $this->filters)->orderBy('name')->get();
    }

    protected function surveyFormOwner(): User
    {
        return auth()->user();
    }

    protected function afterSurveyFormSaved(Survey $survey): void
    {
    }
};
?>

<div
    class="flex flex-1 flex-col"
    x-data
    x-init="
        $wire.syncRecentSearches(JSON.parse(localStorage.getItem('toolify:recent-searches') ?? '[]'));
        $wire.on('recent-searches-changed', ({ searches }) => localStorage.setItem('toolify:recent-searches', JSON.stringify(searches)));
    "
>
    <x-domain.app.topbar.search wire:model.live.debounce.300ms="search" :filters="$filters"/>

    <div class="flex flex-1 flex-col gap-6 px-4 lg:px-8">
        @if ($search !== '' || $this->hasActiveFilters)
            <section>
                <header class="flex items-center justify-between py-3">
                    <h2 class="text-lg font-semibold text-foreground">{{ __('app/search.results_heading') }}</h2>
                </header>

                <div class="flex flex-col divide-y border border-border rounded-md">
                    @forelse ($this->tools as $tool)
                        <x-domain.app.tool-list-item :tool="$tool" :class="trim(($loop->first ? 'rounded-t-md ' : '').($loop->last ? 'rounded-b-md' : ''))"/>
                    @empty
                        <div class="px-6 py-8 text-center text-sm text-muted-foreground">
                            {{ __('app/search.no_results', ['query' => $search]) }}
                        </div>
                    @endforelse
                </div>
            </section>
        @elseif (count($recentSearches))
            <section>
                <header class="flex flex-col items-start gap-2 py-3 lg:flex-row lg:items-center lg:justify-between">
                    <h2 class="text-lg font-semibold text-foreground">{{ __('app/search.recent_searches_heading') }}</h2>
                    <div class="flex w-full flex-wrap items-center gap-2 lg:w-auto">
                        <x-ui.button variant="secondary" icon="tool-view" :label="__('app/search.save_all_as_surveys')" wire:click="saveAllRecentSearchesAsSurveys"/>
                        <x-ui.button :label="__('app/search.clear_history')" wire:click="clearRecentSearches"/>
                    </div>
                </header>

                <div class="flex flex-col gap-5 py-3.5 border border-border rounded-md">
                    @foreach ($recentSearches as $recentSearch)
                        {{-- item--}}
                        <div class="flex flex-wrap items-start gap-y-2">
                            <div class="flex min-w-0 flex-1 items-center">
                                <div class="flex shrink-0 justify-center pl-6 pr-3 h-7 items-center">
                                    <x-ui.button variant="ghost" size="icon-xs" icon="search-01"/>
                                </div>

                                <div class="flex min-w-0 flex-1 flex-col gap-2 justify-center">
                                    <div class="flex items-center px-2 h-7">
                                        <p class="truncate text-sm text-foreground">"{{ $recentSearch['query'] }}"</p>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-1.5 px-2">
                                        @foreach ($recentSearch['filters'] as $filter)
                                            <x-ui.badge>{{ $filter }}</x-ui.badge>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-2 pl-14 pr-6 lg:pl-2">
                                <x-ui.button
                                    variant="secondary"
                                    size="sm"
                                    icon="layer"
                                    :label="__('app/search.save_as_survey')"
                                    wire:click="openSurveyForm(null, @js($recentSearch['query']))"
                                />

                                <x-ui.button variant="ghost" size="sm" icon="more-horizontal-square-01"/>
                            </div>
                        </div>
                    @endforeach
                </div>

                <footer class="py-4">
                    <p class="text-sm text-muted-foreground">{{ __('app/search.retention_note') }}</p>
                </footer>
            </section>

            {{-- TODO : Add recent surveys --}}
        @else
            <div class="flex flex-1 items-center justify-center">
                <x-domain.app.empty-state
                    icon="search-01"
                    :title="__('app/search.empty_state_title')"
                    :description="__('app/search.empty_state_description')"
                />
            </div>
        @endif
    </div>

    @include('components.domain.app.survey-form-modal')
</div>
