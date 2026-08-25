@props([])

@php
    $workspace = auth()->user()->currentWorkspace();
    $teams = $workspace?->teams->filter(fn ($team) => $team->hasMember(auth()->user())) ?? collect();
    $unreadNotifications = auth()->user()->unreadNotifications()->count();
@endphp

<aside {{ $attributes->class('flex h-full w-61 shrink-0 flex-col overflow-visible rounded-sm border border-sidebar-border bg-sidebar') }}>
    <div class="flex h-14 shrink-0 items-center px-3">
        <livewire:domain.app.dropdown.workspace-switcher/>
    </div>

    <nav class="flex min-h-0 flex-1 flex-col items-start overflow-y-auto px-1">
        <x-domain.app.sidebar.group>
            <x-domain.app.sidebar.item icon="search-01" :href="route('search')">{{ __('app/components/sidebar.search') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="discover-circle" :href="route('discovery')">{{ __('app/components/sidebar.discovery') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="inbox" :href="route('inbox')" :badge="$unreadNotifications ?: null">{{ __('app/components/sidebar.inbox') }}</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>

        <x-domain.app.sidebar.group :label="__('app/components/sidebar.personal_group')">
            <x-domain.app.sidebar.item icon="layer" :href="route('stacks.personal')">{{ __('app/components/sidebar.stack') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="tool-view" :href="route('surveys.personal')">{{ __('app/components/sidebar.surveys') }}</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>

        <x-domain.app.sidebar.group :label="__('app/components/sidebar.workspace_group')">
            <x-domain.app.sidebar.item icon="layer" :href="$workspace ? route('stacks.workspace') : null" :disabled="! $workspace">{{ __('app/components/sidebar.stack') }}</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="tool-view" :href="$workspace ? route('surveys.workspace') : null" :disabled="! $workspace">{{ __('app/components/sidebar.surveys') }}</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>

        @if ($teams->isNotEmpty())
            <x-domain.app.sidebar.group :label="__('app/components/sidebar.teams_group')">
                @foreach ($teams as $team)
                    <x-domain.app.sidebar.team :team="$team">
                        <x-domain.app.sidebar.tree-branch>
                            <x-domain.app.sidebar.item icon="layer" :href="route('stacks.teams.show', $team)">{{ __('app/components/sidebar.stack') }}</x-domain.app.sidebar.item>
                        </x-domain.app.sidebar.tree-branch>
                        <x-domain.app.sidebar.tree-branch last>
                            <x-domain.app.sidebar.item icon="tool-view" :href="route('surveys.teams.show', $team)">{{ __('app/components/sidebar.surveys') }}</x-domain.app.sidebar.item>
                        </x-domain.app.sidebar.tree-branch>
                    </x-domain.app.sidebar.team>
                @endforeach
            </x-domain.app.sidebar.group>
        @endif
    </nav>

    <div class="flex shrink-0 items-center justify-between px-4 py-3.5">
        <x-domain.app.sidebar.account/>
        <x-ui.button variant="outline" size="icon-xs" :label="__('app/components/sidebar.help_button')"/>
    </div>
</aside>
