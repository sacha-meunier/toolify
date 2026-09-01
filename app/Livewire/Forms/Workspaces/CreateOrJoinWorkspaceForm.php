<?php

namespace App\Livewire\Forms\Workspaces;

use App\Livewire\Forms\Settings\Images\LogoUpload;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Arr;
use Livewire\Form;

class CreateOrJoinWorkspaceForm extends Form
{
    use LogoUpload;

    public ?User $user = null;

    public string $workspaceChoice = 'create';

    public mixed $workspaceLogo = null;

    public string $workspaceName = '';

    public string $inviteCode = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'workspaceName' => ['required', 'string', 'max:'.Workspace::NAME_MAX_LENGTH],
            'workspaceLogo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'inviteCode' => ['required', 'string'],
        ];
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * Create a new workspace owned by the user, with a unique slug and invite code.
     */
    public function createWorkspace(): Workspace
    {
        $this->validate(Arr::only($this->rules(), ['workspaceName', 'workspaceLogo']));

        $workspace = Workspace::createOwnedBy($this->user, $this->workspaceName);

        if ($this->workspaceLogo) {
            $workspace->update(['logo_url' => $this->storeLogo($this->workspaceLogo, null)]);
        }

        return $workspace;
    }

    /**
     * Join the workspace matching the pasted invite code, adding an "inviteCode" error and
     * returning null when no workspace matches.
     */
    public function joinWorkspace(): ?Workspace
    {
        $this->validate(Arr::only($this->rules(), ['inviteCode']));

        $workspace = Workspace::findByInviteCode($this->inviteCode);

        if (! $workspace) {
            $this->addError('inviteCode', __('app/workspaces/create-or-join.invite_code_invalid'));

            return null;
        }

        $workspace->members()->syncWithoutDetaching($this->user);

        return $workspace;
    }
}
