<?php

namespace App\Livewire\Forms\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Form;

class DeleteAccountForm extends Form
{
    public ?User $user = null;

    public string $password = '';

    public string $confirmation = '';

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function delete(): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password:web'],
            'confirmation' => ['required', 'string', 'in:DELETE'],
        ], [
            'password.current_password' => __('The provided password does not match your current password.'),
            'confirmation.in' => __('Please type DELETE to confirm.'),
        ]);

        Auth::guard('web')->logout();

        $this->user->delete();
    }
}
