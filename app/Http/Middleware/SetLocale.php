<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Applied globally to the "web" middleware group (not just the "{locale}"-prefixed route
     * group), because Livewire's AJAX update endpoint ("/livewire-.../update", used for every
     * component interaction after the initial page load) also runs under "web" but carries no
     * "{locale}" route segment. Without this running there too, actions like the login button
     * would resolve locale-aware redirects (e.g. Fortify's "home") back to their unprefixed
     * default. So locale is remembered in the session on page loads and reused for those.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);
        $request->session()->put('locale', $locale);

        /* Fortify's built-in responses (login intended, 2FA, email verification, ...) fall
         * back to this raw path when no more specific redirect is configured, so it needs
         * to carry the current locale segment rather than the config file's static value. */
        config(['fortify.home' => route('search')]);

        $user = $request->user();

        if ($user && $user->locale !== $locale) {
            $user->forceFill(['locale' => $locale])->save();
        }

        return $next($request);
    }

    /**
     * The "{locale}" route segment takes priority (present on every full page load), falling
     * back to whatever locale the last page load stored in the session (covers Livewire's
     * unprefixed AJAX update endpoint), then to the same preference order as resolve().
     */
    private function resolveLocale(Request $request): string
    {
        $available = array_keys(config('app.available_locales'));

        $routeLocale = $request->route('locale');

        if (is_string($routeLocale) && in_array($routeLocale, $available, true)) {
            return $routeLocale;
        }

        $sessionLocale = $request->session()->get('locale');

        if (is_string($sessionLocale) && in_array($sessionLocale, $available, true)) {
            return $sessionLocale;
        }

        return static::resolve($request);
    }

    /**
     * Resolve the locale to redirect to from the un-prefixed "/" route : the
     * authenticated user's stored preference, then the browser's preferred
     * language, falling back to the app's default locale.
     */
    public static function resolve(Request $request): string
    {
        $available = array_keys(config('app.available_locales'));

        $userLocale = $request->user()?->locale;

        if ($userLocale && in_array($userLocale, $available, true)) {
            return $userLocale;
        }

        return $request->getPreferredLanguage($available) ?? config('app.fallback_locale');
    }
}
