<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-layouts.partials.head :title="$title ?? null"/>

    <x-layouts.partials.body>
        {{ $slot }}
    </x-layouts.partials.body>
</html>
