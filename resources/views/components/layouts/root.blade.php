<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <x-layouts.partials.head/>

    <x-layouts.partials.body>
        {{ $slot }}
    </x-layouts.partials.body>
</html>
