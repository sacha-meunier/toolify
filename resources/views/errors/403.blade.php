<x-layouts.shells.auth>
    {{-- Brand : to be replaced later --}}
    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-sidebar-primary">
                <x-ui.icon.command size="xs" stroke-width="1.5" class="size-6 text-sidebar-primary-foreground"/>
            </span>

    <x-domain.auth.step title="This link isn't valid for your session">
        <div class="flex flex-col gap-4 w-full">
            <p class="text-muted-foreground text-sm text-center">
                This can happen if the link was opened on a different device, in a different
                browser, or after signing in with another account.
            </p>

            <x-ui.button type="a" href="{{ route('home') }}" variant="secondary" size="lg" label="Back to Toolify" class="w-full"/>
        </div>
    </x-domain.auth.step>
</x-layouts.shells.auth>
