@props([])

@php
    $user = auth()->user();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button
        type="button"
        class="flex size-6 shrink-0 items-center justify-center rounded-full border border-border bg-muted text-xs font-medium text-muted-foreground cursor-pointer extend-touch-target"
        @click="open = !open"
    >
        {{ $user->initials() }}
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute bottom-full left-0 z-20 mb-1.5 w-64 overflow-visible rounded-md border border-border bg-popover py-1 shadow-xs"
    >
        <div class="flex items-center gap-2 px-3 py-2">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-medium text-muted-foreground">
                {{ $user->initials() }}
            </div>
            <div class="flex min-w-0 flex-1 flex-col justify-center gap-0.5 leading-none">
                <p class="truncate text-sm font-semibold text-popover-foreground">{{ $user->name }}</p>
                <p class="truncate text-xs text-muted-foreground">{{ $user->email }}</p>
            </div>
        </div>

        <div class="my-1 h-px bg-border"></div>

        <button
            type="button"
            disabled
            class="flex w-full cursor-not-allowed items-center px-3 py-2 text-left text-sm text-muted-foreground opacity-50"
        >
            Settings
        </button>

        <div class="my-1 h-px bg-border"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center px-3 py-2 text-left text-sm text-popover-foreground hover:bg-muted"
            >
                Log out
            </button>
        </form>
    </div>
</div>
