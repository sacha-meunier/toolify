<?php

namespace App\Livewire\Forms\Invitations;

use App\Livewire\Forms\Settings\Images\AvatarUpload;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Livewire\Form;

class InvitationOnboardingForm extends Form
{
    use AvatarUpload;

    public string $password = '';

    public string $name = '';

    public mixed $avatar = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'password' => ['required', 'string', Password::default()],
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function validatePassword(): void
    {
        $this->validate(Arr::only($this->rules(), ['password']));
    }

    /**
     * Create the account with the invitation's (already proven, via the signed link) email,
     * mark it verified and onboarded, store the avatar, and sign the user in.
     */
    public function createAccount(CreatesNewUsers $creator, Invitation $invitation): User
    {
        $this->validate(Arr::only($this->rules(), ['name', 'avatar']));

        $user = $creator->create([
            'name' => $this->name,
            'email' => $invitation->email,
            'password' => $this->password,
            'password_confirmation' => $this->password,
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
            'onboarded_at' => now(),
        ])->save();

        if ($this->avatar) {
            $user->update(['avatar_url' => $this->storeAvatar($this->avatar, null)]);
        }

        Auth::login($user);

        Cookie::queue('last_auth_method', 'email', 60 * 24 * 365);

        return $user;
    }
}
