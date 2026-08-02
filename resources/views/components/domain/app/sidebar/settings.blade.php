@props([])

@php
    $workspace = auth()->user()->currentWorkspace();
    $teams = $workspace?->teams->filter(fn ($team) => $team->hasMember(auth()->user())) ?? collect();
@endphp

<aside {{ $attributes->class('flex h-full w-61 shrink-0 flex-col overflow-visible rounded-sm border border-sidebar-border bg-sidebar') }}>
    <div class="flex h-14 shrink-0 items-center px-3">
        <x-ui.button
            variant="ghost"
            size="lg"
            icon="arrow-left-01"
            :href="route('search')"
            wire:navigate
            class="w-full justify-start gap-2 px-2 font-normal text-sidebar-foreground hover:bg-sidebar-accent"
        >
            Back to app
        </x-ui.button>
    </div>

    <nav class="flex min-h-0 flex-1 flex-col items-start overflow-y-auto px-1">
        <x-domain.app.sidebar.group label="Personal">
            <x-domain.app.sidebar.item :href="route('settings.account.profile')">Profile</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item :href="route('settings.account.security')">Account & Security</x-domain.app.sidebar.item>
            {{--<x-domain.app.sidebar.item :href="route('settings.account.preferences')">Preferences</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item :href="route('settings.account.display')">Display</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item :href="route('settings.account.tracking')">Tracking</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item :href="route('settings.account.notifications')">Notifications</x-domain.app.sidebar.item>--}}
        </x-domain.app.sidebar.group>

        <x-domain.app.sidebar.group label="Workspace">
            <x-domain.app.sidebar.item :href="route('settings.workspace.general')">General</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item href="#" disabled>Members</x-domain.app.sidebar.item>
            {{--<x-domain.app.sidebar.item href="#" disabled>Roles</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item href="#" disabled>Invitations</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item href="#" disabled>Billing</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item href="#" disabled>Activity & Confidentiality</x-domain.app.sidebar.item>--}}
            <x-domain.app.sidebar.item href="#" disabled>Listing</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>

        @foreach ($teams as $team)
            <x-domain.app.sidebar.group :label="$team->name">
                <x-domain.app.sidebar.item href="#" disabled>General</x-domain.app.sidebar.item>
                <x-domain.app.sidebar.item href="#" disabled>Members</x-domain.app.sidebar.item>
                {{--<x-domain.app.sidebar.item href="#" disabled>Roles</x-domain.app.sidebar.item>--}}
                {{--<x-domain.app.sidebar.item href="#" disabled>Invitations</x-domain.app.sidebar.item>--}}
                {{--<x-domain.app.sidebar.item href="#" disabled>Billing</x-domain.app.sidebar.item>--}}
                {{--<x-domain.app.sidebar.item href="#" disabled>Activity & Confidentiality</x-domain.app.sidebar.item>--}}
                <x-domain.app.sidebar.item :href="route('settings.teams.listing.index', $team)">Listing</x-domain.app.sidebar.item>
            </x-domain.app.sidebar.group>
        @endforeach
    </nav>
</aside>
