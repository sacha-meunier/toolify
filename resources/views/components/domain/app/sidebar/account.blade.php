@props([])

@php
    $user = auth()->user();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button
        type="button"
        class="flex size-6 shrink-0 items-center justify-center overflow-clip rounded-full border border-border bg-muted text-xs font-medium text-muted-foreground cursor-pointer extend-touch-target"
        @click="open = !open"
    >
        @if ($user->avatar_url)
            <img src="{{ $user->avatar_url }}" alt="" class="size-full object-cover">
        @else
            {{ $user->initials() }}
        @endif
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute bottom-full left-0 z-20 mb-1.5 w-64 overflow-visible rounded-md border border-border bg-popover py-1 shadow-xs"
    >
        <div class="flex items-center gap-2 px-3 py-2">
            <div class="flex size-8 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-sm font-medium text-muted-foreground">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="" class="size-full object-cover">
                @else
                    {{ $user->initials() }}
                @endif
            </div>
            <div class="flex min-w-0 flex-1 flex-col justify-center gap-0.5 leading-none">
                <p class="truncate text-sm font-semibold text-popover-foreground">{{ $user->name }}</p>
                <p class="truncate text-xs text-muted-foreground">{{ $user->email }}</p>
            </div>
        </div>

        <div class="my-1 h-px bg-border"></div>

        <x-ui.button :href="route('settings.account.profile')" wire:navigate :label="__('app/components/sidebar/account.settings_link')" variant="ghost" class="w-full justify-start"></x-ui.button>

        <div class="my-1 h-px bg-border"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center px-3 py-2 text-left text-sm text-popover-foreground hover:bg-muted"
            >
                {{ __('app/components/sidebar/account.log_out_button') }}
            </button>
        </form>
    </div>
</div>
