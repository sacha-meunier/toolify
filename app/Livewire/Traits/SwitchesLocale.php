<?php

namespace App\Livewire\Traits;

/**
 * Adds a `setLocale()` action to redirect to the same page under a
 * different "{locale}" prefix.
 */
trait SwitchesLocale
{
    public function setLocale(string $locale): mixed
    {
        abort_unless(array_key_exists($locale, config('app.available_locales')), 404);

        $segments = explode('/', trim((string) parse_url((string) url()->previous(), PHP_URL_PATH), '/'));
        $segments[0] = $locale;

        return redirect('/'.implode('/', $segments));
    }
}
