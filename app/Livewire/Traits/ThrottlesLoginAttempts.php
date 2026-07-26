<?php

namespace App\Livewire\Traits;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

/**
 * Throttle login attempts for a given email using the `login` rate limiter
 * registered in FortifyServiceProvider, without routing through Fortify's own
 * controller.
 */
trait ThrottlesLoginAttempts
{
    /**
     * @throws ValidationException
     */
    protected function ensureLoginIsNotRateLimited(string $email): void
    {
        $limit = $this->loginRateLimit($email);

        if (! RateLimiter::tooManyAttempts($limit->key, $limit->maxAttempts)) {
            return;
        }

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => RateLimiter::availableIn($limit->key),
            ]),
        ]);
    }

    protected function incrementLoginAttempts(string $email): void
    {
        $limit = $this->loginRateLimit($email);

        RateLimiter::hit($limit->key, $limit->decaySeconds);
    }

    protected function clearLoginAttempts(string $email): void
    {
        RateLimiter::clear($this->loginRateLimit($email)->key);
    }

    private function loginRateLimit(string $email): Limit
    {
        return RateLimiter::limiter('login')(
            request()->merge([Fortify::username() => $email])
        );
    }
}
