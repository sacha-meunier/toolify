@props(['last' => false])

<div class="relative flex w-full flex-col items-start pl-6">
    <span class="absolute left-[18px] top-0 h-[18px] w-3 rounded-bl-xl border-b border-l border-sidebar-border"></span>

    @unless ($last)
        <span class="absolute left-[18px] top-[5px] bottom-0 w-px bg-sidebar-border"></span>
    @endunless

    {{ $slot }}
</div>
