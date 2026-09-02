<?php

use App\Models\Team;
use App\Models\Tool;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public Tool $tool;

    #[Computed]
    public function workspace(): ?Workspace
    {
        return auth()->user()->currentWorkspace();
    }

    /**
     * The teams, within the current workspace, whose stack this user can manage.
     *
     * @return Collection<int, Team>
     */
    #[Computed]
    public function teams(): Collection
    {
        return $this->workspace
            ? $this->workspace->teams()->get()->filter(fn (Team $team) => Gate::allows('manageStack', $team))->values()
            : new Collection;
    }

    public function inPersonalStack(): bool
    {
        return auth()->user()->hasInStack($this->tool);
    }

    public function inWorkspaceStack(): bool
    {
        return (bool) $this->workspace?->hasInStack($this->tool);
    }

    public function inTeamStack(Team $team): bool
    {
        return $team->hasInStack($this->tool);
    }

    public function toggle(string $scope, ?int $id = null): void
    {
        $target = match ($scope) {
            'personal' => auth()->user(),
            'workspace' => $this->workspace,
            'team' => Team::findOrFail($id),
            default => null,
        };

        abort_if($target === null, 404);

        if ($target instanceof Workspace || $target instanceof Team) {
            Gate::authorize('manageStack', $target);
        }

        $target->stackOrCreate()->tools()->toggle($this->tool->id);

        unset($this->workspace, $this->teams);
    }
};
?>

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <x-ui.button
        variant="primary"
        icon="layer"
        :label="__('app/components/dropdown/add-to-stack-menu.cta_add_to_stack')"
        @click="open = !open"
    />

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute right-0 z-10 mt-1.5 w-64 overflow-clip rounded-md border border-border bg-popover p-1 shadow-xs"
    >
        <button
            type="button"
            wire:click="toggle('personal')"
            class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
        >
            <x-ui.icon.checkmark-circle-02
                size="sm"
                class="shrink-0 {{ $this->inPersonalStack() ? 'text-primary' : 'text-transparent' }}"
            />
            <span class="truncate">{{ __('app/components/dropdown/add-to-stack-menu.my_stack') }}</span>
        </button>

        @if ($this->workspace)
            <div class="-mx-1 my-1 h-px bg-border"></div>

            <button
                type="button"
                wire:click="toggle('workspace')"
                class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
            >
                <x-ui.icon.checkmark-circle-02
                    size="sm"
                    class="shrink-0 {{ $this->inWorkspaceStack() ? 'text-primary' : 'text-transparent' }}"
                />
                <span class="truncate">{{ __('app/components/dropdown/add-to-stack-menu.workspace', ['name' => $this->workspace->name]) }}</span>
            </button>

            @if ($this->teams->isNotEmpty())
                <div class="-mx-1 my-1 h-px bg-border"></div>

                @foreach ($this->teams as $team)
                    <button
                        type="button"
                        wire:click="toggle('team', {{ $team->id }})"
                        class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                    >
                        <x-ui.icon.checkmark-circle-02
                            size="sm"
                            class="shrink-0 {{ $this->inTeamStack($team) ? 'text-primary' : 'text-transparent' }}"
                        />
                        <span class="truncate">{{ __('app/components/dropdown/add-to-stack-menu.team', ['name' => $team->name]) }}</span>
                    </button>
                @endforeach
            @endif
        @endif
    </div>
</div>
