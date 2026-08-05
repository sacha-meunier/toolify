<?php

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
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
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm font-medium text-foreground">{{ $team->name }}'s stack</p>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-6 px-8 py-8">
        <x-domain.app.tool-list
            :tools="$this->tools"
            empty-icon="layer"
            empty-title="This team's stack is empty"
            empty-description="Tools saved to {{ $team->name }} will show up here."
        />
    </div>
</div>
