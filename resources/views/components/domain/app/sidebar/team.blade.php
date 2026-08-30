@props(['team'])

@php
    $active = request()->route('team')?->is($team) ?? false;
@endphp

<div class="mt-2 flex w-full flex-col items-start first:mt-0">
    <div @class([
        'flex h-8 w-full shrink-0 items-center gap-2 px-2 text-sm font-medium',
        'text-sidebar-foreground' => ! $active,
        'text-sidebar-accent-foreground' => $active,
    ])>
        <span class="flex size-5 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-[10px] font-medium text-muted-foreground">
            @if ($team->logo_url)
                <img src="{{ $team->logo_url }}" alt="" class="size-full object-cover">
            @else
                {{ $team->initials() }}
            @endif
        </span>
        <span class="truncate">{{ $team->name }}</span>
    </div>

    <div class="flex w-full flex-col items-start">
        {{ $slot }}
    </div>
</div>
