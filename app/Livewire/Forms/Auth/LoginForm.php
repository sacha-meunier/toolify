<?php

namespace App\Livewire\Forms\Auth;

use Livewire\Form;

class LoginForm extends Form
{
    public string $email = '';

    public string $password = '';

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
