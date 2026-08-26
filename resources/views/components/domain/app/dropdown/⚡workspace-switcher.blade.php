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

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <x-ui.button
        variant="ghost"
        size="sm"
        icon:trailing="arrow-down-01"
        class="w-full min-w-0 gap-2 px-1.5 font-semibold text-sidebar-foreground"
        @click="open = !open"
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
        class="absolute left-0 z-20 mt-1.5 w-64 overflow-visible rounded-md border border-border bg-background py-1 shadow-xs"
    >
        <div class="relative" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
            <button
                type="button"
                class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
            >
                <span class="truncate">{{ __('app/components/dropdown/workspace-switcher.switch_workspace') }}</span>
                <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 opacity-50"/>
            </button>

            <div
                x-show="hover"
                x-cloak
                x-transition
                class="absolute left-full top-0 ml-1 w-56 overflow-hidden rounded-md border border-border bg-background py-1 shadow-xs"
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

        <div class="my-1 h-px bg-border"></div>

        <a
            href="{{ route('settings.workspace.members') }}"
            wire:navigate
            class="flex w-full items-center px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
        >
            {{ __('app/components/dropdown/workspace-switcher.invite_and_manage_members') }}
        </a>

        <a
            href="{{ route('settings.workspace.general') }}"
            wire:navigate
            class="flex w-full items-center px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
        >
            {{ __('app/components/dropdown/workspace-switcher.settings') }}
        </a>
    </div>
</div>
