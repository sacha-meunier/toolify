<?php

use App\Livewire\Forms\Settings\InviteMemberForm;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.settings')]
class extends Component {
    public Workspace $workspace;

    public InviteMemberForm $inviteForm;

    public bool $inviteMemberModalOpen = false;

    public function mount(): void
    {
        $workspace = auth()->user()->currentWorkspace();

        abort_if(! $workspace, 404);

        Gate::authorize('manageMembers', $workspace);

        $this->workspace = $workspace;
        $this->inviteForm->setContext($workspace);
    }

    public function openInviteMemberModal(): void
    {
        Gate::authorize('manageMembers', $this->workspace);

        $this->inviteMemberModalOpen = true;
    }

    public function closeInviteMemberModal(): void
    {
        $this->inviteMemberModalOpen = false;
        $this->inviteForm->reset();
        $this->resetValidation();
    }

    public function inviteMember(): void
    {
        Gate::authorize('manageMembers', $this->workspace);

        if ($this->inviteForm->invite()) {
            $this->inviteMemberModalOpen = false;
            unset($this->members);
        }
    }

    /**
     * @return Collection<int, object{user: User, role: string, joinedAt: \Illuminate\Support\Carbon, removable: bool}>
     */
    #[Computed]
    public function members(): Collection
    {
        $members = $this->workspace->members()->orderBy('name')->get()
            ->map(fn (User $user) => (object) [
                'user' => $user,
                'role' => __('app/settings/workspace/members.role_member'),
                'joinedAt' => $user->pivot->created_at,
                'removable' => true,
            ]);

        if ($owner = $this->workspace->owner) {
            $members->prepend((object) [
                'user' => $owner,
                'role' => __('app/settings/workspace/members.role_owner'),
                'joinedAt' => $this->workspace->created_at,
                'removable' => false,
            ]);
        }

        return $members;
    }

    public function removeMember(User $user): void
    {
        Gate::authorize('manageMembers', $this->workspace);

        $this->workspace->members()->detach($user);

        unset($this->members);
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/workspace/members.breadcrumb_settings') => null,
            __('app/settings/workspace/members.breadcrumb_workspace') => null,
            __('app/settings/workspace/members.breadcrumb_members') => null,
        ]"/>

        <x-slot:actions>
            <x-ui.button variant="primary" size="sm" icon="add-01" :label="__('app/settings/workspace/members.invite_member')" wire:click="openInviteMemberModal"/>
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-10 py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/workspace/members.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/workspace/members.description', ['name' => $workspace->name]) }}</p>
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

                    <p class="mr-2 w-20 shrink-0 text-right text-xs text-muted-foreground">{{ __('app/settings/workspace/members.joined_on', ['date' => $member->joinedAt->format('M j')]) }}</p>

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
                                    wire:confirm="{{ __('app/settings/workspace/members.remove_member_confirm', ['member' => $member->user->name, 'workspace' => $workspace->name]) }}"
                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-destructive hover:bg-muted"
                                >
                                    <x-ui.icon.delete-02 size="sm" class="shrink-0"/>
                                    <span>{{ __('app/settings/workspace/members.delete') }}</span>
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

    <x-ui.modal show="$wire.inviteMemberModalOpen" close="closeInviteMemberModal" class="max-w-md">
        <x-slot:header>
            <h2 class="text-lg font-semibold text-foreground">{{ __('app/settings/workspace/members.invite_modal_heading') }}</h2>
        </x-slot:header>

        <form wire:submit="inviteMember" class="flex flex-col gap-4">
            <x-ui.field>
                <x-ui.field.label :content="__('app/settings/workspace/members.email_label')"/>
                <x-ui.input type="email" wire:model="inviteForm.email" name="inviteForm.email" :placeholder="__('app/settings/workspace/members.email_placeholder')" autofocus/>
                <x-ui.field.error :content="$errors->first('inviteForm.email')"/>
            </x-ui.field>

            <div class="flex items-center justify-end gap-2 pt-2">
                <x-ui.button type="button" variant="outline" :label="__('app/settings/workspace/members.cancel')" wire:click="closeInviteMemberModal"/>
                <x-ui.button type="submit" variant="primary" :label="__('app/settings/workspace/members.send_invite')"/>
            </div>
        </form>
    </x-ui.modal>
</div>
