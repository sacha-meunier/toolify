@props(['title' => null])

<x-layouts.root :title="$title">
    <x-layouts.partials.nav-public/>

    <main class="pt-14">{{ $slot }}</main>
</x-layouts.root>
