@props([])

@php
    $workspace = auth()->user()->currentWorkspace();
    $teams = $workspace?->teams->filter(fn ($team) => $team->hasMember(auth()->user())) ?? collect();
@endphp

<aside
    {{ $attributes->class('fixed inset-y-0 left-0 z-50 flex h-dvh w-72 shrink-0 -translate-x-full flex-col overflow-visible border-r border-sidebar-border bg-sidebar transition-transform duration-300 ease-in-out lg:static lg:inset-auto lg:z-10 lg:h-full lg:w-61 lg:translate-x-0 lg:rounded-sm lg:border') }}
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="flex h-14 shrink-0 items-center px-3">
        <livewire:domain.app.dropdown.settings-workspace-switcher :key="'settings-workspace-switcher-'.$workspace?->id"/>
    </div>

    <div class="flex h-14 shrink-0 items-center px-3">
        <x-ui.button
            variant="nav"
            size="lg"
            icon="arrow-left-01"
            :href="route('search')"
            wire:navigate
            class="w-full justify-start gap-2 px-2 font-normal text-sidebar-foreground hover:bg-sidebar-accent-hover hover:text-sidebar-accent-foreground"
        >
            {{ __('app/components/sidebar/settings.back_to_app_button') }}
        </x-ui.button>
    </div>

    <nav class="flex min-h-0 flex-1 flex-col items-start overflow-y-auto px-1">
        <x-domain.app.sidebar.group :label="__('app/components/sidebar/settings.personal_group')">
            <x-domain.app.sidebar.item icon="user" :href="route('settings.account.profile')">{{ __('app/components/sidebar/settings.profile') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="square-lock-02" :href="route('settings.account.security')">{{ __('app/components/sidebar/settings.account_security') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="sliders-horizontal" :href="route('settings.account.preferences')">{{ __('app/components/sidebar/settings.preferences') }}</x-domain.app.sidebar.item>
            {{--<x-domain.app.sidebar.item icon="computer" :href="route('settings.account.display')">Display</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item icon="activity-01" :href="route('settings.account.tracking')">Tracking</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item icon="notification-01" :href="route('settings.account.notifications')">Notifications</x-domain.app.sidebar.item>--}}
        </x-domain.app.sidebar.group>

        <x-domain.app.sidebar.group :label="__('app/components/sidebar/settings.workspace_group')">
            <x-domain.app.sidebar.item icon="settings-01" :href="route('settings.workspace.general')">{{ __('app/components/sidebar/settings.general') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="user-group" :href="route('settings.workspace.members')">{{ __('app/components/sidebar/settings.members') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="layer" :href="route('settings.workspace.teams.index')">{{ __('app/components/sidebar/settings.teams') }}</x-domain.app.sidebar.item>
            {{--<x-domain.app.sidebar.item href="#" disabled>Roles</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item href="#" disabled>Invitations</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item href="#" disabled>Billing</x-domain.app.sidebar.item>--}}
            {{--<x-domain.app.sidebar.item href="#" disabled>Activity & Confidentiality</x-domain.app.sidebar.item>--}}
            <x-domain.app.sidebar.item icon="identity-card" href="#" disabled>{{ __('app/components/sidebar/settings.listing') }}</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>

        @if ($teams->isNotEmpty())
            <x-domain.app.sidebar.group :label="__('app/components/sidebar/settings.teams_group')">
                @foreach ($teams as $team)
                    <x-domain.app.sidebar.team :team="$team">
                        <x-domain.app.sidebar.tree-branch>
                            <x-domain.app.sidebar.item icon="settings-01" :href="route('settings.teams.general', $team)">{{ __('app/components/sidebar/settings.general') }}</x-domain.app.sidebar.item>
                        </x-domain.app.sidebar.tree-branch>
                        <x-domain.app.sidebar.tree-branch>
                            <x-domain.app.sidebar.item icon="user-group" :href="route('settings.teams.members', $team)">{{ __('app/components/sidebar/settings.members') }}</x-domain.app.sidebar.item>
                        </x-domain.app.sidebar.tree-branch>
                        <x-domain.app.sidebar.tree-branch last>
                            <x-domain.app.sidebar.item icon="identity-card" :href="route('settings.teams.listing.index', $team)">{{ __('app/components/sidebar/settings.listing') }}</x-domain.app.sidebar.item>
                        </x-domain.app.sidebar.tree-branch>
                    </x-domain.app.sidebar.team>
                @endforeach
            </x-domain.app.sidebar.group>
        @endif
    </nav>
</aside>
