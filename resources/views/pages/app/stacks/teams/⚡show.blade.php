<?php

use App\Livewire\Traits\BuildsPageTitle;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use BuildsPageTitle;

    public Team $team;

    public function mount(Team $team): void
    {
        Gate::authorize('manageStack', $team);

        $this->team = $team;
    }

    #[Computed]
    public function tools(): Collection
    {
        return $this->team->stack?->tools()->withTrashed()->get() ?? new Collection;
    }

    public function render()
    {
        return $this->view()->title($this->pageTitle(__('app/components/sidebar.stack'), $this->team->name));
    }
};
?>

<div class="flex flex-1 flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm font-medium text-foreground">{{ __('app/stacks/teams/show.title', ['name' => $team->name]) }}</p>
    </x-domain.app.topbar>

    <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8 lg:py-8">
        <x-domain.app.tool-list
            :tools="$this->tools"
            empty-icon="layer"
            :empty-title="__('app/stacks/teams/show.empty_title')"
            :empty-description="__('app/stacks/teams/show.empty_description', ['name' => $team->name])"
        />
    </div>
</div>
