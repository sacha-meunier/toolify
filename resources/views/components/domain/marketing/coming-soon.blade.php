@blaze()

@props([
    'badge' => 'Coming soon',
    'title' => null,
    'description' => null,
])

<div class="flex min-h-[60vh] flex-col items-center justify-center gap-2 px-4 text-center">
    <p class="text-sm font-medium text-primary">{{ $badge }}</p>
    <h1 class="text-2xl font-semibold text-foreground">{{ $title }}</h1>
    <p class="max-w-md text-muted-foreground">{{ $description }}</p>
    <x-ui.button variant="primary" class="mt-4" :href="route('register')" label="Get started free"/>
</div>
