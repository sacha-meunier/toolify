<?php

namespace App\Livewire\Forms\Onboarding;

use App\Livewire\Forms\Settings\Images\AvatarUpload;
use App\Livewire\Forms\Settings\Images\LogoUpload;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

    /**
     * Create a new workspace owned by the user, with a unique slug and invite code.
     */
    public function createWorkspace(): Workspace
    {
        $this->validate(Arr::only($this->rules(), ['workspaceName', 'workspaceLogo']));

        $workspace = Workspace::create([
            'name' => $this->workspaceName,
            'slug' => $this->generateUniqueSlug($this->workspaceName),
            'owner_id' => $this->user->id,
            'invite_code' => Workspace::generateUniqueInviteCode(),
        ]);

        if ($this->workspaceLogo) {
            $workspace->update(['logo_url' => $this->storeLogo($this->workspaceLogo, null)]);
        }

        return $workspace;
    }

    /**
     * Join the workspace matching the pasted invite code, adding an "inviteCode" error and
     * returning false when no workspace matches.
     */
    public function joinWorkspace(): bool
    {
        $this->validate(Arr::only($this->rules(), ['inviteCode']));

        $workspace = Workspace::where('invite_code', Str::upper(trim($this->inviteCode)))->first();

        if (! $workspace) {
            $this->addError('inviteCode', __('app/onboarding/index.invite_code_invalid'));

            return false;
        }

        $workspace->members()->syncWithoutDetaching($this->user);

        return true;
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);

        $unique = $slug;

        while (Workspace::where('slug', $unique)->exists()) {
            $unique = $slug.'-'.random_int(1, 1000000);
        }

        return $unique;
    }
}
