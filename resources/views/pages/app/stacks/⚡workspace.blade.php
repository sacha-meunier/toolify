<?php

use App\Livewire\Traits\BuildsPageTitle;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use BuildsPageTitle;

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

    public function render()
    {
        return $this->view()->title($this->pageTitle(__('app/components/sidebar.stack'), $this->workspace?->name));
    }
};
?>

<div class="flex flex-1 flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm font-medium text-foreground">
            {{ $this->workspace ? __('app/stacks/workspace.title', ['name' => $this->workspace->name]) : __('app/stacks/workspace.title_fallback') }}
        </p>
    </x-domain.app.topbar>

    <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8 lg:py-8">
        @if ($this->workspace)
            <x-domain.app.tool-list
                :tools="$this->tools"
                empty-icon="layer"
                :empty-title="__('app/stacks/workspace.empty_title')"
                :empty-description="__('app/stacks/workspace.empty_description')"
            />
        @else
            <div class="flex flex-1 items-center justify-center">
                <x-domain.app.empty-state
                    icon="layer"
                    :title="__('app/stacks/workspace.no_workspace_title')"
                    :description="__('app/stacks/workspace.no_workspace_description')"
                />
            </div>
        @endif
    </div>
</div>
