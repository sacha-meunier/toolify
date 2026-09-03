<?php

use App\Livewire\Traits\BuildsPageTitle;
use App\Models\Team;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')]
class extends Component {
    use BuildsPageTitle;

    public Workspace $workspace;

    public function mount(): void
    {
        $workspace = auth()->user()->currentWorkspace();

        abort_if(! $workspace, 404);

        Gate::authorize('manageWorkspace', $workspace);

        $this->workspace = $workspace;
    }

    /**
     * @return Collection<int, Team>
     */
    #[Computed]
    public function teams(): Collection
    {
        return $this->workspace->teams()
            ->orderBy('name')
            ->get()
            ->filter(fn (Team $team): bool => $team->hasMember(auth()->user()))
            ->values();
    }

    public function render()
    {
        return $this->view()->title($this->pageTitle(__('app/settings/workspace/teams/index.heading'), $this->workspace->name));
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/workspace/teams/index.breadcrumb_settings') => null,
            __('app/settings/workspace/teams/index.breadcrumb_workspace') => null,
            __('app/settings/workspace/teams/index.breadcrumb_teams') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" icon="add-01" :label="__('app/settings/workspace/teams/index.create_team')" :href="route('settings.workspace.teams.create')" wire:navigate/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/workspace/teams/index.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/workspace/teams/index.description', ['name' => $workspace->name]) }}</p>
        </header>

        @if ($this->teams->isEmpty())
            <x-domain.app.settings.section>
                <x-domain.app.settings.section-content :label="__('app/settings/workspace/teams/index.empty_label')" :description="__('app/settings/workspace/teams/index.empty_description')">
                    <x-ui.button variant="outline" size="sm" :label="__('app/settings/workspace/teams/index.create_team')" :href="route('settings.workspace.teams.create')" wire:navigate/>
                </x-domain.app.settings.section-content>
            </x-domain.app.settings.section>
        @else
            <div class="flex w-full flex-col divide-y divide-border overflow-clip rounded-xl border border-border bg-card shadow-xs">
                @foreach ($this->teams as $team)
                    <a href="{{ route('settings.teams.general', $team) }}" wire:navigate class="flex items-center gap-3 px-4 py-3 hover:bg-muted/50">
                        <div class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-lg bg-muted text-sm font-medium text-muted-foreground">
                            @if ($team->logo_url)
                                <img src="{{ $team->logo_url }}" alt="" class="size-full object-cover">
                            @else
                                {{ $team->initials() }}
                            @endif
                        </div>

                        @php $membersCount = $team->totalMemberCount(); @endphp
                        <div class="flex min-w-0 flex-1 flex-col">
                            <p class="truncate text-sm font-medium text-foreground">{{ $team->name }}</p>
                            <p class="truncate text-xs text-muted-foreground">{{ trans_choice('app/settings/workspace/teams/index.members_count', $membersCount, ['count' => $membersCount]) }}</p>
                        </div>

                        <x-ui.icon.arrow-right-01 size="xs" class="shrink-0 text-muted-foreground"/>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
