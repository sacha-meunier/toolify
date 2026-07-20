<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', val) })"
    :class="{ 'dark': darkMode }"
>
    <x-layouts.partials.head/>

    <x-layouts.partials.body>
        {{ $slot }}
    </x-layouts.partials.body>
</html>
