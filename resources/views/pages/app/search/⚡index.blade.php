<?php

use App\Enums\Category;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public const int MAX_RECENT_SEARCHES = 10;

    public const int RECENT_SEARCHES_RETENTION_DAYS = 14;

    public string $search = '';

    /**
     * Hydrated from the browser's localStorage on load via syncRecentSearches(),
     * since this history is per-device and shouldn't round-trip on every request.
     */
    public array $recentSearches = [];

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
        $matchingCategories = collect(Category::cases())
            ->filter(fn (Category $category) => str_contains(strtolower($category->label()), strtolower($this->search)))
            ->map(fn (Category $category) => $category->value);

        return Tool::query()
            ->where(fn ($query) => $query
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('tagline', 'like', "%{$this->search}%")
                ->when($matchingCategories->isNotEmpty(), fn ($query) => $query->orWhere(
                    fn ($query) => $matchingCategories->each(
                        fn (string $category) => $query->orWhereJsonContains('categories', $category)
                    )
                ))
            )
            ->orderBy('name')
            ->get();
    }
};
?>

<div
    class="flex flex-col"
    x-data
    x-init="
        $wire.syncRecentSearches(JSON.parse(localStorage.getItem('toolify:recent-searches') ?? '[]'));
        $wire.on('recent-searches-changed', ({ searches }) => localStorage.setItem('toolify:recent-searches', JSON.stringify(searches)));
    "
>
    <x-domain.app.topbar.search wire:model.live.debounce.300ms="search"/>

    <div class="flex flex-col gap-6 px-8">
        @if ($search !== '')
            <section>
                <header class="flex items-center justify-between py-3">
                    <h2 class="text-lg font-semibold text-foreground">Results</h2>
                </header>

                <div class="flex flex-col divide-y border border-border rounded-md">
                    @forelse ($this->tools as $tool)
                        <div class="flex items-center gap-3 px-6 py-3.5 hover:bg-muted">
                            <a href="{{ route('tools.show', $tool) }}" wire:navigate class="flex min-w-0 flex-1 flex-col gap-2">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-medium text-foreground">{{ $tool->name }}</p>
                                    <p class="truncate text-sm text-muted-foreground">{{ $tool->tagline }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-1.5">
                                    @foreach ($tool->categories as $category)
                                        <x-ui.badge>{{ $category->label() }}</x-ui.badge>
                                    @endforeach
                                    <x-ui.badge>{{ $tool->pricing->label() }}</x-ui.badge>
                                </div>
                            </a>

                            <x-ui.button
                                variant="secondary"
                                size="sm"
                                :href="$tool->website_url"
                                target="_blank"
                                label="Visit"
                            />
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-sm text-muted-foreground">
                            No tools match "{{ $search }}".
                        </div>
                    @endforelse
                </div>
            </section>
        @else
            <section>
                <header class="flex items-center justify-between py-3">
                    <h2 class="text-lg font-semibold text-foreground">Recent searches</h2>
                    @if (count($recentSearches))
                        <div class="flex items-center gap-2">
                            <x-ui.button variant="secondary" icon="layer" label="Save all as views"/>
                            <x-ui.button label="Clear history" wire:click="clearRecentSearches"/>
                        </div>
                    @endif
                </header>

                <div class="flex flex-col gap-5 py-3.5 border border-border rounded-md">
                    @forelse ($recentSearches as $recentSearch)
                        {{-- item--}}
                        <div class="flex">
                            <div class="flex shrink-0 justify-center pl-6 pr-3 h-7">
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

                            <div class="flex shrink-0 px-2 h-7">
                                <x-ui.button variant="secondary" size="sm" icon="layer" label="Save as a view"/>
                            </div>

                            <div class="flex shrink-0 justify-center pl-3 pr-6 h-7">
                                <x-ui.button variant="ghost" size="sm" icon="more-horizontal-square-01"/>
                            </div>
                        </div>
                    @empty
                        <x-domain.app.empty-state
                            icon="search-01"
                            title="No recent searches"
                            description="Searches you run will show up here so you can jump back into them or save them as views."
                        />
                    @endforelse
                </div>

                @if (count($recentSearches))
                    <footer class="py-4">
                        <p class="text-sm text-muted-foreground">Max 10 recent searches · each saved locally for 14
                            days</p>
                    </footer>
                @endif
            </section>

            {{-- TODO : Add recent views --}}
        @endif
    </div>
</div>
