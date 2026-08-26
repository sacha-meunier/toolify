<?php

namespace App\Livewire\Forms\Settings;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\TeamMemberAddedNotification;
use Livewire\Form;

class InviteMemberForm extends Form
{
    public ?Workspace $workspace = null;

    public ?Team $team = null;

    public string $email = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function setContext(Workspace $workspace, ?Team $team = null): void
    {
        $this->workspace = $workspace;
        $this->team = $team;
    }

    /**
     * Invite a member to the team/workspace :
     *
     * if user is part of the team's workspace he is added directly ;
     * if user is not part of the team's workspace he receives an invitation in his inbox ;
     * if user is not part of Toolify he receives an email.
     */
    public function invite(): bool
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if ($user && $this->alreadyMember($user)) {
            $this->addError('email', $this->team
                ? 'This person is already a member of the team.'
                : 'This person is already a member of the workspace.');

            return false;
        }

        if ($this->team && $user && $this->workspace->hasMember($user)) {
            $this->addWorkspaceMemberToTeam($user);

            return true;
        }

        $this->sendInvitation();

        return true;
    }

    /**
     * Whether the user is already a member of the invite's target (the team, or the workspace
     * when there's no team).
     */
    protected function alreadyMember(User $user): bool
    {
        return $this->team
            ? $this->team->hasMember($user)
            : $this->workspace->hasMember($user);
    }

    /**
     * Attach a user who's already a workspace member directly to the team, skipping the
     * invitation flow.
     */
    protected function addWorkspaceMemberToTeam(User $user): void
    {
        $this->team->members()->attach($user);

        $user->notify(new TeamMemberAddedNotification($this->team, auth()->user()));

        $this->reset('email');
    }

    /**
     * Create or update a pending invitation and notify the invitee in-app for existing
     * users, by email for anyone without a Toolify account yet.
     */
    protected function sendInvitation(): void
    {
        $invitation = Invitation::updateOrCreate(
            ['workspace_id' => $this->workspace->id, 'team_id' => $this->team?->id, 'email' => $this->email],
            ['invited_by_id' => auth()->id(), 'status' => InvitationStatus::Pending, 'dismissed_at' => null],
        );

        $invitation->notifyInvitee();

        $this->reset('email');
    }
}
