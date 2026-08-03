<?php

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')]
class extends Component {
    public Team $team;

    public function mount(Team $team): void
    {
        Gate::authorize('manageMembers', $team);

        $this->team = $team;
    }

    /**
     * @return Collection<int, object{user: User, role: string, joinedAt: \Illuminate\Support\Carbon, removable: bool}>
     */
    #[Computed]
    public function members(): Collection
    {
        $members = $this->team->members()->orderBy('name')->get()
            ->map(fn (User $user) => (object) [
                'user' => $user,
                'role' => 'Member',
                'joinedAt' => $user->pivot->created_at,
                'removable' => true,
            ]);

        if ($owner = $this->team->workspace->owner) {
            $members->prepend((object) [
                'user' => $owner,
                'role' => 'Owner',
                'joinedAt' => $this->team->created_at,
                'removable' => false,
            ]);
        }

        return $members;
    }

    public function removeMember(User $user): void
    {
        Gate::authorize('manageMembers', $this->team);

        $this->team->members()->detach($user);

        unset($this->members);
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            'Settings' => null,
            'Teams' => null,
            $team->name => null,
            'Members' => null,
        ]"/>

        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" icon="add-01" label="Invite member" disabled/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">Members</h1>
            <p class="text-sm text-muted-foreground">People with access to {{ $team->name }}.</p>
        </header>

        <div class="flex w-full flex-col divide-y divide-border overflow-visible rounded-xl border border-border bg-card shadow-xs">
            @foreach ($this->members as $member)
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-sm font-medium text-muted-foreground">
                        @if ($member->user->avatar_url)
                            <img src="{{ $member->user->avatar_url }}" alt="" class="size-full object-cover">
                        @else
                            {{ $member->user->initials() }}
                        @endif
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col">
                        <p class="truncate text-sm font-medium text-foreground">{{ $member->user->name }}</p>
                        <p class="truncate text-xs text-muted-foreground">{{ $member->user->email }}</p>
                    </div>

                    <p class="mr-2 w-20 shrink-0 text-right text-xs text-muted-foreground">Joined {{ $member->joinedAt->format('M j') }}</p>

                    <x-ui.badge>{{ $member->role }}</x-ui.badge>

                    @if ($member->removable)
                        <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                            <x-ui.button variant="ghost" size="icon-sm" icon="more-horizontal-square-01" @click="open = !open"/>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition
                                class="absolute right-0 z-10 mt-1.5 w-44 overflow-clip rounded-md border border-border bg-background py-1 shadow-xs"
                            >
                                <button
                                    type="button"
                                    wire:click="removeMember({{ $member->user->id }})"
                                    wire:confirm="Remove {{ $member->user->name }} from {{ $team->name }}?"
                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-destructive hover:bg-muted"
                                >
                                    <x-ui.icon.delete-02 size="sm" class="shrink-0"/>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="size-8 shrink-0"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
