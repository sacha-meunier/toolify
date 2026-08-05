<?php

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function workspace(): ?Workspace
    {
        return auth()->user()->currentWorkspace();
    }

    #[Computed]
    public function tools(): Collection
    {
        return $this->workspace?->stack?->tools()->withTrashed()->get() ?? new Collection;
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm font-medium text-foreground">
            {{ $this->workspace ? "{$this->workspace->name}'s stack" : 'Workspace stack' }}
        </p>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-6 px-8 py-8">
        @if ($this->workspace)
            <x-domain.app.tool-list
                :tools="$this->tools"
                empty-icon="computer-terminal-01"
                empty-title="This workspace's stack is empty"
                empty-description="Tools saved to the workspace will show up here."
            />
        @else
            <x-domain.app.empty-state
                icon="computer-terminal-01"
                title="You're not part of a workspace yet"
                description="Join or create a workspace to start building its stack."
            />
        @endif
    </div>
</div>
