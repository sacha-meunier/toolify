<?php

namespace App\Livewire\Forms\Auth;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;
use Livewire\Form;

class RegisterForm extends Form
{
    public string $email = '';

    public string $password = '';

    /**
     * @return array<string, array<int, string|Unique|Password>>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', Password::default()],
        ];
    }
}
