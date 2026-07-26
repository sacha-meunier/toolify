<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\Computed;

/**
 * Determine if an auth method (email, Google, Github) has been used previously.
 * (if any) displays it in the Login and Signup screens, just below it's respective action button (e.g Continue with Email).
 */
trait LastAuthMethodUsed
{
    public ?string $lastAuthMethod = null;

    // Check if there's a `last_auth_method` stored in the cookie.
    public function mountLastAuthMethodUsed(): void
    {
        $this->lastAuthMethod = request()->cookie('last_auth_method');
    }

    /**
     * The email/Google/Github methods, with whichever one was last used to log in
     * (if any) surfaced first.
     */
    #[Computed]
    public function authMethods(): array
    {
        $methods = [
            'email' => ['label' => 'Continue with email', 'action' => 'continueWithEmail'],
            'google' => ['label' => 'Continue with google', 'action' => null],
            'github' => ['label' => 'Continue with github', 'action' => null],
        ];

        if ($this->lastAuthMethod && isset($methods[$this->lastAuthMethod])) {
            $methods = [$this->lastAuthMethod => $methods[$this->lastAuthMethod]] + $methods;
        }

        return $methods;
    }
}
