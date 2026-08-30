<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-layouts.partials.head/>

    <x-layouts.partials.body>
        {{ $slot }}
    </x-layouts.partials.body>
</html>
