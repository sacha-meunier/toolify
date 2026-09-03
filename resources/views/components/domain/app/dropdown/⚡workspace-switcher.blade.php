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
        variant="nav"
        size="sm"
        icon:trailing="arrow-down-01"
        class="w-full min-w-0 gap-2 px-1.5 font-semibold text-sidebar-accent-foreground hover:bg-sidebar-accent/45"
        @click="open = !open"
    >
        <span class="flex size-5 shrink-0 items-center justify-center overflow-clip rounded-lg bg-muted text-[10px] font-medium text-muted-foreground">
            @if ($this->workspace->logo_url)
                <img src="{{ $this->workspace->logo_url }}" alt="" class="size-full object-cover">
            @else
                {{ $this->workspace->initials() }}
            @endif
        </span>
        <span class="min-w-0 flex-1 truncate text-left">{{ $this->workspace->name }}</span>
    </x-ui.button>

    <x-ui.dropdown-panel
        x-show="open"
        class="absolute left-0 z-20 mt-1.5 max-h-[70vh] w-64 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-md border border-border bg-popover p-1 shadow-xs lg:max-h-none lg:max-w-none lg:overflow-visible"
    >
        <div class="relative" x-data="{ expanded: false }" @mouseenter="if (window.innerWidth >= 1024) expanded = true" @mouseleave="if (window.innerWidth >= 1024) expanded = false">
            <button
                type="button"
                @click="expanded = ! expanded"
                class="flex w-full items-center justify-between rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
            >
                <span class="truncate">{{ __('app/components/dropdown/workspace-switcher.switch_workspace') }}</span>
                <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 rotate-90 opacity-50 transition-transform lg:rotate-0" x-bind:class="{ '-rotate-90': expanded }"/>
            </button>

            <div
                x-show="expanded"
                x-cloak
                x-transition:enter="transition ease-out-strong duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="origin-top bg-muted/30 py-1 lg:absolute lg:top-0 lg:left-full lg:z-30 lg:ml-1 lg:w-56 lg:origin-top-left lg:overflow-hidden lg:rounded-md lg:border lg:border-border lg:bg-popover lg:p-1 lg:shadow-xs"
            >
                <div class="max-h-56 overflow-y-auto">
                    @forelse ($this->workspaces as $workspace)
                        <button
                            type="button"
                            wire:click="switchTo({{ $workspace->id }})"
                            class="flex w-full items-center gap-2 rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                        >
                            <x-ui.icon.checkmark-circle-02
                                size="sm"
                                class="shrink-0 {{ $this->workspace?->id === $workspace->id ? 'text-primary' : 'text-transparent' }}"
                            />
                            <span class="flex size-5 shrink-0 items-center justify-center overflow-clip rounded-lg bg-muted text-[10px] font-medium text-muted-foreground">
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

                <div class="my-1 h-px bg-border lg:-mx-1"></div>

                <a
                    href="{{ route('workspaces.create-or-join') }}"
                    wire:navigate
                    class="flex w-full items-center rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                >
                    {{ __('app/components/dropdown/workspace-switcher.create_or_join_workspace') }}
                </a>
            </div>
        </div>

        <div class="-mx-1 my-1 h-px bg-border"></div>

        <a
            href="{{ route('settings.workspace.members') }}"
            wire:navigate
            class="flex w-full items-center rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
        >
            {{ __('app/components/dropdown/workspace-switcher.invite_and_manage_members') }}
        </a>

        <a
            href="{{ route('settings.workspace.general') }}"
            wire:navigate
            class="flex w-full items-center rounded px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
        >
            {{ __('app/components/dropdown/workspace-switcher.settings') }}
        </a>
    </x-ui.dropdown-panel>
</div>
