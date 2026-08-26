<?php

namespace App\Livewire\Forms\Onboarding;

use App\Livewire\Forms\Settings\Images\AvatarUpload;
use App\Livewire\Forms\Settings\Images\LogoUpload;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Arr;
use Livewire\Form;

class OnboardingForm extends Form
{
    use AvatarUpload, LogoUpload;

    public ?User $user = null;

    public mixed $avatar = null;

    public string $name = '';

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
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'workspaceName' => ['required', 'string', 'max:255'],
            'workspaceLogo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'inviteCode' => ['required', 'string'],
        ];
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
    }

    /**
     * Save the user's fullname and avatar from the profile step.
     */
    public function saveProfile(): void
    {
        $this->validate(Arr::only($this->rules(), ['name', 'avatar']));

        $this->user->update(['name' => $this->name]);

        if ($this->avatar) {
            $this->user->update(['avatar_url' => $this->storeAvatar($this->avatar, $this->user->avatar_url)]);
        }
    }

    public function deleteAvatar(): void
    {
        if (! $this->user->avatar_url) {
            return;
        }

        $this->deleteAvatarFile($this->user->avatar_url);

        $this->user->update(['avatar_url' => null]);
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
            $this->addError('inviteCode', __('app/onboarding/index.invite_code_invalid'));

            return null;
        }

        $workspace->members()->syncWithoutDetaching($this->user);

        return $workspace;
    }
}
