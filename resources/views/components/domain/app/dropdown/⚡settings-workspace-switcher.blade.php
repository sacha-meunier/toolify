<?php

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    /**
     * @return Collection<int, Workspace>
     */
    #[Computed]
    public function workspaces(): Collection
    {
        $currentId = $this->workspace?->id;

        return auth()->user()->accessibleWorkspaces()
            ->sortBy(fn (Workspace $workspace) => $workspace->id === $currentId ? 0 : 1)
            ->values();
    }

    #[Computed]
    public function workspace(): ?Workspace
    {
        return auth()->user()->currentWorkspace();
    }

    public function switchTo(int $workspaceId): void
    {
        $workspace = $this->workspaces->firstWhere('id', $workspaceId);

        abort_if($workspace === null, 404);

        session(['current_workspace_id' => $workspace->id]);

        $this->redirect(url()->previous(), navigate: true);
    }
};
?>

<div
    class="relative"
    x-data="{ open: false, position: { top: 0, left: 0 } }"
    @click.outside="open = false"
>
    <x-ui.button
        variant="nav"
        size="sm"
        icon:trailing="arrow-down-01"
        class="w-full min-w-0 gap-2 px-1.5 font-semibold text-sidebar-accent-foreground hover:bg-sidebar-accent/45"
        @click="
            const rect = $el.getBoundingClientRect();
            position = { top: rect.bottom + window.scrollY + 4, left: Math.min(rect.left + window.scrollX, window.innerWidth - 272) };
            open = ! open;
        "
    >
        <span class="flex size-5 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-[10px] font-medium text-muted-foreground">
            @if ($this->workspace->logo_url)
                <img src="{{ $this->workspace->logo_url }}" alt="" class="size-full object-cover">
            @else
                {{ $this->workspace->initials() }}
            @endif
        </span>
        <span class="min-w-0 flex-1 truncate text-left">{{ $this->workspace->name }}</span>
    </x-ui.button>

    <div
        x-show="open"
        x-cloak
        x-transition
        x-bind:style="`position: fixed; top: ${position.top}px; left: ${position.left}px;`"
        class="z-50 w-64 overflow-hidden rounded-md border border-border bg-popover py-1 shadow-md"
    >
        <div class="max-h-56 overflow-y-auto">
            @forelse ($this->workspaces as $workspace)
                <button
                    type="button"
                    wire:click="switchTo({{ $workspace->id }})"
                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                >
                    <x-ui.icon.checkmark-circle-02
                        size="sm"
                        class="shrink-0 {{ $this->workspace?->id === $workspace->id ? 'text-primary' : 'text-transparent' }}"
                    />
                    <span class="flex size-5 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-[10px] font-medium text-muted-foreground">
                        @if ($workspace->logo_url)
                            <img src="{{ $workspace->logo_url }}" alt="" class="size-full object-cover">
                        @else
                            {{ $workspace->initials() }}
                        @endif
                    </span>
                    <span class="truncate">{{ $workspace->name }}</span>
                </button>
            @empty
                <p class="px-3 py-2 text-sm text-muted-foreground">{{ __('app/components/dropdown/workspace-switcher.no_workspace') }}</p>
            @endforelse
        </div>

        <div class="my-1 h-px bg-border"></div>

        <a
            href="{{ route('workspaces.create-or-join') }}"
            wire:navigate
            class="flex w-full items-center px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
        >
            {{ __('app/components/dropdown/workspace-switcher.create_or_join_workspace') }}
        </a>
    </div>
</div>
