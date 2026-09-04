@props(['title' => null])

<x-layouts.root :title="$title">
    <div class="min-h-screen lg:grid lg:grid-cols-2">
        <div class="flex items-center justify-center">{{ $slot }}</div>
        <div class="hidden lg:block">{{ $banner }}</div>
    </div>
</x-layouts.root>
