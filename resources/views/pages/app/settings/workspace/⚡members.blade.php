<?php

use App\Enums\InvitationStatus;
use App\Livewire\Forms\Settings\InviteMemberForm;
use App\Models\Invitation;
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
        Gate::authorize('inviteMembers', $this->workspace);

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
        Gate::authorize('inviteMembers', $this->workspace);

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

        $this->workspace->removeMember($user);

        unset($this->members);
    }

    /**
     * @return Collection<int, object{invitation: Invitation, cancelled: bool}>
     */
    #[Computed]
    public function pendingInvitations(): Collection
    {
        Gate::authorize('inviteMembers', $this->workspace);

        return $this->workspace->invitations()
            ->whereNull('team_id')
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
        Gate::authorize('inviteMembers', $this->workspace);

        $invitation->dismiss();

        unset($this->pendingInvitations);
    }
};
?>

<div class="flex flex-col" x-data="{ confirmMemberId: null, confirmMemberMessage: null, confirmMemberOpen: false, confirmInvitationId: null, confirmInvitationMessage: null, confirmInvitationOpen: false }">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[
            __('app/settings/workspace/members.breadcrumb_settings') => null,
            __('app/settings/workspace/members.breadcrumb_workspace') => null,
            __('app/settings/workspace/members.breadcrumb_members') => null,
        ]"/>

        @can('inviteMembers', $workspace)
            <x-slot:actions>
                <x-ui.button variant="primary" size="sm" icon="add-01" :label="__('app/settings/workspace/members.invite_member')" wire:click="openInviteMemberModal"/>
            </x-slot:actions>
        @endcan
    </x-domain.app.topbar>

    <div class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-6 lg:px-10 lg:py-10">
        <header class="flex flex-col gap-1 px-4">
            <h1 class="text-3xl font-semibold text-foreground">{{ __('app/settings/workspace/members.heading') }}</h1>
            <p class="text-sm text-muted-foreground">{{ __('app/settings/workspace/members.description', ['name' => $workspace->name]) }}</p>
        </header>

        <x-domain.app.settings.section>
            <x-domain.app.settings.section-content
                :label="__('app/settings/workspace/members.invite_code_label')"
                :description="__('app/settings/workspace/members.invite_code_description', ['name' => $workspace->name])"
            >
                <div class="flex w-full items-center gap-2 lg:w-auto" x-data="{ copied: false }">
                    <div class="flex h-8 flex-1 items-center rounded-lg border border-input bg-secondary px-2.5 font-mono text-sm text-foreground lg:flex-none">
                        {{ $workspace->invite_code }}
                    </div>

                    <x-ui.button
                        type="button"
                        variant="outline"
                        size="icon"
                        x-on:click="navigator.clipboard.writeText('{{ $workspace->invite_code }}'); copied = true; setTimeout(() => copied = false, 1500)"
                        :aria-label="__('app/settings/workspace/members.copy_invite_code')"
                    >
                        <x-ui.icon.copy-01 x-show="!copied" size="sm"/>
                        <x-ui.icon.checkmark-circle-02 x-show="copied" x-cloak size="sm"/>
                    </x-ui.button>
                </div>
            </x-domain.app.settings.section-content>
        </x-domain.app.settings.section>

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
                        <p class="shrink-0 whitespace-nowrap text-right text-xs text-muted-foreground">{{ __('app/settings/workspace/members.joined_on', ['date' => $member->joinedAt->format('M j')]) }}</p>

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
                                            confirmMemberMessage = @js(__('app/settings/workspace/members.remove_member_confirm', ['member' => $member->user->name, 'workspace' => $workspace->name]));
                                            confirmMemberOpen = true
                                        "
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
                </div>
            @endforeach
        </div>

        @can('inviteMembers', $workspace)
            @if ($this->pendingInvitations->isNotEmpty())
                <x-domain.app.settings.section
                    :label="__('app/settings/workspace/members.pending_invitations_heading')"
                    :description="__('app/settings/workspace/members.pending_invitations_description')"
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
                                    <p class="truncate text-xs text-muted-foreground">{{ __('app/settings/workspace/members.invited_on', ['date' => $invitation->created_at->format('M j')]) }}</p>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-3 pl-12 lg:pl-0">
                                <x-ui.badge :class="$pending->cancelled ? 'bg-muted text-muted-foreground' : ''">
                                    {{ __('app/settings/workspace/members.status_'.($pending->cancelled ? 'cancelled' : 'pending')) }}
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
                                                confirmInvitationMessage = @js(__('app/settings/workspace/members.dismiss_invitation_confirm', ['email' => $invitation->email]));
                                                confirmInvitationOpen = true
                                            "
                                            class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-foreground hover:bg-muted"
                                        >
                                            <x-ui.icon.eye-off size="sm" class="shrink-0"/>
                                            <span>{{ __('app/settings/workspace/members.hide_invitation') }}</span>
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
            <h2 class="text-lg font-semibold text-foreground">{{ __('app/settings/workspace/members.invite_modal_heading') }}</h2>
        </x-slot:header>

        <form wire:submit="inviteMember" class="flex flex-col gap-4">
            <x-ui.field>
                <x-ui.field.label :content="__('app/settings/workspace/members.email_label')" required/>
                <x-ui.input type="email" wire:model="inviteForm.email" name="inviteForm.email" :placeholder="__('app/settings/workspace/members.email_placeholder')" autofocus required/>
                <x-ui.field.error :content="$errors->first('inviteForm.email')"/>
            </x-ui.field>

            <div class="flex items-center justify-end gap-2 pt-2">
                <x-ui.button type="button" variant="outline" :label="__('app/settings/workspace/members.cancel')" wire:click="closeInviteMemberModal"/>
                <x-ui.button type="submit" variant="primary" :label="__('app/settings/workspace/members.send_invite')"/>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.confirm-modal
        show="confirmMemberOpen"
        :heading="__('app/settings/workspace/members.remove_member_modal_heading')"
        :cancel-label="__('app/settings/workspace/members.cancel')"
    >
        <x-slot:body>
            <p class="text-sm text-muted-foreground" x-text="confirmMemberMessage"></p>
        </x-slot:body>

        <x-ui.button variant="destructive" :label="__('app/settings/workspace/members.delete')" @click="$wire.call('removeMember', confirmMemberId); confirmMemberOpen = false"/>
    </x-ui.confirm-modal>

    <x-ui.confirm-modal
        show="confirmInvitationOpen"
        :heading="__('app/settings/workspace/members.dismiss_invitation_modal_heading')"
        :cancel-label="__('app/settings/workspace/members.cancel')"
    >
        <x-slot:body>
            <p class="text-sm text-muted-foreground" x-text="confirmInvitationMessage"></p>
        </x-slot:body>

        <x-ui.button variant="primary" :label="__('app/settings/workspace/members.hide_invitation')" @click="$wire.call('dismissInvitation', confirmInvitationId); confirmInvitationOpen = false"/>
    </x-ui.confirm-modal>
</div>
