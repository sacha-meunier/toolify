@blaze()

@props([
    'badge' => null,
    'title' => null,
    'description' => null,
])

<div class="flex min-h-[calc(100dvh-3.5rem)] flex-col items-center justify-center gap-2 px-4 text-center">
    <p class="text-sm font-medium text-primary">{{ $badge ?? __('public/components/coming-soon.badge') }}</p>
    <h1 class="text-2xl font-semibold text-foreground">{{ $title }}</h1>
    <p class="max-w-md text-muted-foreground">{{ $description }}</p>
    <x-ui.button variant="primary" class="mt-4" :href="route('register')" :label="__('public/components/coming-soon.cta_get_started')"/>
</div>
