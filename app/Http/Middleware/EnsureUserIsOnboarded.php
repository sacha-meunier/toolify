<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOnboarded
{
    /**
     * Redirect authenticated users who haven't completed the onboarding wizard yet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->onboarded_at) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
