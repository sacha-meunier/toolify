<?php

use App\Enums\InvitationStatus;
use App\Livewire\Forms\Settings\InviteMemberForm;
use App\Models\Invitation;
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

    public InviteMemberForm $inviteForm;

    public bool $inviteMemberModalOpen = false;

    public function mount(Team $team): void
    {
        Gate::authorize('manageMembers', $team);

        $this->team = $team;
        $this->inviteForm->setContext($team->workspace, $team);
    }

    public function openInviteMemberModal(): void
    {
        Gate::authorize('inviteMembers', $this->team);

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
        Gate::authorize('inviteMembers', $this->team);

        if ($this->inviteForm->invite()) {
            $this->inviteMemberModalOpen = false;
            unset($this->members);
            unset($this->pendingInvitations);
        }
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
                'role' => __('app/settings/teams/members.role_member'),
                'joinedAt' => $user->pivot->created_at,
                'removable' => true,
            ]);

        if ($owner = $this->team->workspace->owner) {
            $members->prepend((object) [
                'user' => $owner,
                'role' => __('app/settings/teams/members.role_owner'),
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

    /**
     * @return Collection<int, object{invitation: Invitation, cancelled: bool}>
     */
    #[Computed]
    public function pendingInvitations(): Collection
    {
        Gate::authorize('inviteMembers', $this->team);

        return $this->team->invitations()
            ->whereIn('status', [InvitationStatus::Pending, InvitationStatus::Declined])
            ->whereNull('dismissed_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Invitation $invitation) => (object) [
                'invitation' => $invitation,
                'cancelled' => $invitation->status === InvitationStatus::Declined,
            ]);
    }

    public function dismissInvitation(Invitation $invitation): void
    {
        Gate::authorize('inviteMembers', $this->team);

        $invitation->dismiss();

        unset($this->pendingInvitations);
    }
};
?>

<div class="flex flex-col" x-data="{ confirmMemberId: null, confirmMemberMessage: null, confirmMemberOpen: false, confirmInvitationId: null, confirmInvitationMessage: null, confirmInvitationOpen: false }">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/teams/members.breadcrumb_settings') => null,
            __('app/settings/teams/members.breadcrumb_teams') => null,
            $team->name => null,
            __('app/settings/teams/members.breadcrumb_members') => null,
        ]"/>

        @can('inviteMembers', $team)
            <x-slot:actions>
                <x-ui.button variant="primary" size="sm" icon="add-01" :label="__('app/settings/teams/members.invite_member')" wire:click="openInviteMemberModal"/>
            </x-slot:actions>
        @endcan
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/teams/members.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/teams/members.description', ['name' => $team->name]) }}</p>
        </header>

        <div class="flex w-full flex-col divide-y divide-border overflow-visible rounded-xl border border-border bg-card shadow-xs">
            @foreach ($this->members as $member)
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 px-4 py-3">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
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
                    </div>

                    <div class="flex shrink-0 items-center gap-3 pl-12 lg:pl-0">
                        <p class="shrink-0 whitespace-nowrap text-right text-xs text-muted-foreground">{{ __('app/settings/teams/members.joined_on', ['date' => $member->joinedAt->format('M j')]) }}</p>

                        <x-ui.badge>{{ $member->role }}</x-ui.badge>

                        @if ($member->removable)
                            <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                                <x-ui.button variant="ghost" size="icon-sm" icon="more-horizontal-square-01" @click="open = !open"/>

                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition
                                    class="absolute right-0 z-10 mt-1.5 w-44 overflow-clip rounded-md border border-border bg-popover py-1 shadow-xs"
                                >
                                    <button
                                        type="button"
                                        @click="
                                            open = false;
                                            confirmMemberId = {{ $member->user->id }};
                                            confirmMemberMessage = @js(__('app/settings/teams/members.remove_member_confirm', ['member' => $member->user->name, 'team' => $team->name]));
                                            confirmMemberOpen = true
                                        "
                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-destructive hover:bg-muted"
                                    >
                                        <x-ui.icon.delete-02 size="sm" class="shrink-0"/>
                                        <span>{{ __('app/settings/teams/members.delete') }}</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="size-8 shrink-0"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @can('inviteMembers', $team)
            @if ($this->pendingInvitations->isNotEmpty())
                <x-domain.app.settings.section
                    :label="__('app/settings/teams/members.pending_invitations_heading')"
                    :description="__('app/settings/teams/members.pending_invitations_description')"
                >
                    @foreach ($this->pendingInvitations as $pending)
                        @php $invitation = $pending->invitation; @endphp
                        <div wire:key="pending-invitation-{{ $invitation->id }}" class="flex flex-wrap items-center gap-x-3 gap-y-2 px-4 py-3">
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-muted-foreground">
                                    <x-ui.icon.user size="sm"/>
                                </div>

                                <div class="flex min-w-0 flex-1 flex-col">
                                    <p class="truncate text-sm font-medium text-foreground">{{ $invitation->email }}</p>
                                    <p class="truncate text-xs text-muted-foreground">{{ __('app/settings/teams/members.invited_on', ['date' => $invitation->created_at->format('M j')]) }}</p>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-3 pl-12 lg:pl-0">
                                <x-ui.badge :class="$pending->cancelled ? 'bg-muted text-muted-foreground' : ''">
                                    {{ __('app/settings/teams/members.status_'.($pending->cancelled ? 'cancelled' : 'pending')) }}
                                </x-ui.badge>

                                <div class="relative shrink-0" x-data="{ open: false }" @click.outside="open = false">
                                    <x-ui.button variant="ghost" size="icon-sm" icon="more-horizontal-square-01" @click="open = !open"/>

                                    <div
                                        x-show="open"
                                        x-cloak
                                        x-transition
                                        class="absolute right-0 z-10 mt-1.5 w-56 overflow-clip rounded-md border border-border bg-popover py-1 shadow-xs"
                                    >
                                        <button
                                            type="button"
                                            @click="
                                                open = false;
                                                confirmInvitationId = {{ $invitation->id }};
                                                confirmInvitationMessage = @js(__('app/settings/teams/members.dismiss_invitation_confirm', ['email' => $invitation->email]));
                                                confirmInvitationOpen = true
                                            "
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                                        >
                                            <x-ui.icon.eye-off size="sm" class="shrink-0"/>
                                            <span>{{ __('app/settings/teams/members.hide_invitation') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </x-domain.app.settings.section>
            @endif
        @endcan
    </div>

    <x-ui.modal show="$wire.inviteMemberModalOpen" close="closeInviteMemberModal" class="max-w-md">
        <x-slot:header>
            <h2 class="text-lg font-semibold text-foreground">{{ __('app/settings/teams/members.invite_modal_heading') }}</h2>
        </x-slot:header>

        <form wire:submit="inviteMember" class="flex flex-col gap-4">
            <x-ui.field>
                <x-ui.field.label :content="__('app/settings/teams/members.email_label')" required/>
                <x-ui.input type="email" wire:model="inviteForm.email" name="inviteForm.email" :placeholder="__('app/settings/teams/members.email_placeholder')" autofocus required/>
                <x-ui.field.error :content="$errors->first('inviteForm.email')"/>
            </x-ui.field>

            <div class="flex items-center justify-end gap-2 pt-2">
                <x-ui.button type="button" variant="outline" :label="__('app/settings/teams/members.cancel')" wire:click="closeInviteMemberModal"/>
                <x-ui.button type="submit" variant="primary" :label="__('app/settings/teams/members.send_invite')"/>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.confirm-modal
        show="confirmMemberOpen"
        :heading="__('app/settings/teams/members.remove_member_modal_heading')"
        :cancel-label="__('app/settings/teams/members.cancel')"
    >
        <x-slot:body>
            <p class="text-sm text-muted-foreground" x-text="confirmMemberMessage"></p>
        </x-slot:body>

        <x-ui.button variant="destructive" :label="__('app/settings/teams/members.delete')" @click="$wire.call('removeMember', confirmMemberId); confirmMemberOpen = false"/>
    </x-ui.confirm-modal>

    <x-ui.confirm-modal
        show="confirmInvitationOpen"
        :heading="__('app/settings/teams/members.dismiss_invitation_modal_heading')"
        :cancel-label="__('app/settings/teams/members.cancel')"
    >
        <x-slot:body>
            <p class="text-sm text-muted-foreground" x-text="confirmInvitationMessage"></p>
        </x-slot:body>

        <x-ui.button variant="primary" :label="__('app/settings/teams/members.hide_invitation')" @click="$wire.call('dismissInvitation', confirmInvitationId); confirmInvitationOpen = false"/>
    </x-ui.confirm-modal>
</div>
