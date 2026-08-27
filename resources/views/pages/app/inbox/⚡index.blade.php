<?php

use App\Enums\InvitationStatus;
use App\Enums\NotificationType;
use App\Models\Invitation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::shells.app-inbox')]
class extends Component {
    public ?DatabaseNotification $notification = null;

    public ?Invitation $invitation = null;

    public function mount(?DatabaseNotification $notification = null): void
    {
        if ($notification) {
            $notification = auth()->user()->notifications()->whereKey($notification->id)->firstOrFail();

            if (! $notification->read_at) {
                $notification->markAsRead();
            }

            if ($notification->data['type'] === NotificationType::InvitationReceived->value) {
                $this->invitation = Invitation::find($notification->data['invitation_id']);
            }
        }

        $this->notification = $notification;
    }

    public function acceptInvitation(): void
    {
        $this->invitation->acceptFor(auth()->user());
        $this->invitation->refresh();
    }

    public function declineInvitation(): void
    {
        $this->invitation->declineFor(auth()->user());
        $this->invitation->refresh();
    }

    /**
     * @return Collection<int, DatabaseNotification>
     */
    #[Computed]
    public function notifications(): Collection
    {
        return auth()->user()->notifications;
    }
};
?>

<x-slot:list>
    <div class="flex h-14 shrink-0 items-center border-b border-border p-4">
        <p class="text-sm font-medium text-foreground">{{ __('app/inbox.title') }}</p>
    </div>

    <div class="flex flex-col gap-1 p-2">
        @forelse ($this->notifications as $item)
            <a
                href="{{ route('inbox', $item) }}"
                wire:navigate
                class="flex w-full items-start gap-3 rounded-lg p-2 text-left {{ $notification?->id === $item->id ? 'bg-muted' : 'hover:bg-muted/50' }}"
            >
                <div class="flex size-8 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-xs font-medium text-muted-foreground">
                    @if ($item->data['avatar_url'] ?? null)
                        <img src="{{ $item->data['avatar_url'] }}" alt="" class="size-full object-cover">
                    @elseif ($item->data['avatar_initials'] ?? null)
                        {{ $item->data['avatar_initials'] }}
                    @else
                        <x-dynamic-component :component="'ui.icon.'.NotificationType::from($item->data['type'])->icon()" size="sm"/>
                    @endif
                </div>

                <div class="flex min-w-0 flex-1 flex-col gap-1">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-foreground">{{ $item->data['title'] }}</p>
                            @if (! $item->read_at)
                                <span class="size-2 shrink-0 rounded-full bg-blue-500"></span>
                            @endif
                        </div>
                        <p class="shrink-0 text-xs text-muted-foreground">{{ $item->created_at->diffForHumans(null, true, true) }}</p>
                    </div>
                    <p class="line-clamp-2 text-xs text-muted-foreground">{{ $item->data['body'] }}</p>
                </div>
            </a>
        @empty
            <p class="px-1 py-8 text-center text-sm text-muted-foreground">{{ __('app/inbox.empty_list') }}</p>
        @endforelse
    </div>
</x-slot:list>

<div class="flex min-h-0 flex-1 flex-col">
    @if ($notification)
        <x-domain.app.topbar>
            <x-domain.app.topbar.breadcrumb :items="[__('app/inbox.title') => route('inbox'), $notification->data['title'] => null]"/>
        </x-domain.app.topbar>

        <div class="mx-auto flex w-full max-w-4xl flex-col gap-4 px-4 py-6 lg:px-10 lg:py-10">
            <header class="flex flex-col gap-1">
                <p class="text-xs text-muted-foreground">{{ $notification->created_at->diffForHumans(null, true, true) }}</p>
                <h1 class="text-2xl font-semibold text-foreground">{{ $notification->data['title'] }}</h1>
            </header>

            <p class="text-sm text-muted-foreground">{{ $notification->data['body'] }}</p>

            @if ($invitation)
                @if ($invitation->status === InvitationStatus::Pending)
                    <div class="flex items-center gap-2">
                        <x-ui.button variant="primary" size="sm" :label="__('app/inbox.accept')" wire:click="acceptInvitation"/>
                        <x-ui.button variant="outline" size="sm" :label="__('app/inbox.decline')" wire:click="declineInvitation"/>
                    </div>
                @else
                    <div>
                        <x-ui.badge>{{ $invitation->status->label() }}</x-ui.badge>
                    </div>
                @endif
            @elseif ($url = $notification->data['url'] ?? null)
                <div>
                    <x-ui.button variant="outline" size="sm" :label="__('app/inbox.view')" :href="$url" wire:navigate/>
                </div>
            @endif
        </div>
    @else
        <div class="flex flex-1 items-center justify-center">
            <x-domain.app.empty-state
                icon="notification-01"
                :title="__('app/inbox.empty_state_title')"
                :description="__('app/inbox.empty_state_description')"
            />
        </div>
    @endif
</div>
