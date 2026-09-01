<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Forms\Settings\Images\AvatarUpload;
use App\Models\User;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Form;

class ProfileForm extends Form
{
    use AvatarUpload;

    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public mixed $avatar = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:'.User::NAME_MAX_LENGTH],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function update(): void
    {
        $this->validate();

        app(UpdatesUserProfileInformation::class)->update($this->user, [
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->avatar) {
            $this->user->update([
                'avatar_url' => $this->storeAvatar($this->avatar, $this->user->avatar_url),
            ]);

            $this->reset('avatar');
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
}
