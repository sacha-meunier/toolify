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
            {{ $this->workspace ? __('app/stacks/workspace.title', ['name' => $this->workspace->name]) : __('app/stacks/workspace.title_fallback') }}
        </p>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-6 px-4 py-6 lg:px-8 lg:py-8">
        @if ($this->workspace)
            <x-domain.app.tool-list
                :tools="$this->tools"
                empty-icon="layer"
                :empty-title="__('app/stacks/workspace.empty_title')"
                :empty-description="__('app/stacks/workspace.empty_description')"
            />
        @else
            <x-domain.app.empty-state
                icon="layer"
                :title="__('app/stacks/workspace.no_workspace_title')"
                :description="__('app/stacks/workspace.no_workspace_description')"
            />
        @endif
    </div>
</div>
