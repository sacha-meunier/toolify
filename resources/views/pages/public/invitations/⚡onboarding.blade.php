<?php

use App\Enums\InvitationStatus;
use App\Livewire\Forms\Invitations\InvitationOnboardingForm;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Transition;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.auth')]
class extends Component {
    use WithFileUploads;

    public Invitation $invitation;

    public string $step = 'welcome';

    public ?string $blocked = null;

    public InvitationOnboardingForm $form;

    public function mount(Invitation $invitation): void
    {
        $this->invitation = $invitation;

        if ($invitation->status !== InvitationStatus::Pending) {
            $this->blocked = 'handled';

            return;
        }

        if (auth()->check()) {
            if (auth()->user()->email !== $invitation->email) {
                $this->blocked = 'wrong-account';

                return;
            }

            $invitation->acceptFor(auth()->user());
            $this->redirect($this->postAcceptUrl(), navigate: true);

            return;
        }

        if (User::where('email', $invitation->email)->exists()) {
            session(['url.intended' => url()->current()]);
            $this->redirect(route('login'), navigate: true);

            return;
        }

        $this->form->name = Str::before($invitation->email, '@');
    }

    #[Computed]
    public function memberCount(): int
    {
        return $this->invitation->workspace->totalMemberCount();
    }

    #[Computed]
    public function teamMemberCount(): int
    {
        return $this->invitation->team->totalMemberCount();
    }

    #[Transition(type: 'forward')]
    public function continueFromWelcome(): void
    {
        $this->step = 'password';
    }

    #[Transition(type: 'backward')]
    public function backToWelcome(): void
    {
        $this->step = 'welcome';
    }

    #[Transition(type: 'forward')]
    public function continueFromPassword(): void
    {
        $this->form->validatePassword();

        $this->step = 'profile';
    }

    #[Transition(type: 'backward')]
    public function backToPassword(): void
    {
        $this->step = 'password';
    }

    /**
     * Create the account, mark the invitation accepted and send him to the discovery page.
     */
    public function finish(CreatesNewUsers $creator): void
    {
        $user = $this->form->createAccount($creator, $this->invitation);

        $this->invitation->acceptFor($user);

        $this->redirect($this->postAcceptUrl(), navigate: true);
    }

    protected function postAcceptUrl(): string
    {
        return route('discovery');
    }
};
?>

<div class="flex flex-col gap-6">
    {{-- Brand : to be replaced later --}}
    <x-ui.icon.command class="size-12 block self-center"/>

    <div wire:transition="content" class="w-full">
        @if ($blocked === 'handled')
            <div class="flex flex-col gap-1">
                <h1 class="text-foreground text-center font-semibold text-lg">{{ __('public/invitations/onboarding.status_heading', ['status' => $invitation->status->label()]) }}</h1>
                <p class="text-muted-foreground text-sm text-center">
                    {{ __('public/invitations/onboarding.status_description', ['target' => $invitation->target()->name, 'status' => strtolower($invitation->status->label())]) }}
                </p>
            </div>
        @elseif ($blocked === 'wrong-account')
            <div class="flex flex-col gap-1">
                <h1 class="text-foreground text-center font-semibold text-lg">{{ __('public/invitations/onboarding.wrong_account_heading') }}</h1>
                <p class="text-muted-foreground text-sm text-center">
                    {{ __('public/invitations/onboarding.wrong_account_description', ['email' => $invitation->email]) }}
                </p>
            </div>
        @elseif ($step === 'welcome')
            <div class="flex flex-col gap-6" wire:key="step-welcome">
                <div class="flex flex-col gap-1">
                    <h1 class="text-foreground text-center font-semibold text-lg">{{ __('public/invitations/onboarding.welcome_heading') }}</h1>
                    <p class="text-muted-foreground text-sm text-center">
                        {{ __('public/invitations/onboarding.welcome_description', ['inviter' => $invitation->invitedBy->name, 'target' => $invitation->target()->name]) }}
                    </p>
                </div>

                <div class="flex w-full flex-col rounded-xl border border-border bg-card p-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-sm font-medium text-muted-foreground">
                            @if ($invitation->workspace->logo_url)
                                <img src="{{ $invitation->workspace->logo_url }}" alt="" class="size-full object-cover">
                            @else
                                {{ $invitation->workspace->initials() }}
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col">
                            <p class="truncate text-sm font-medium text-foreground">{{ $invitation->workspace->name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $this->memberCount }} {{ Str::plural(__('public/invitations/onboarding.member'), $this->memberCount) }}</p>
                        </div>
                    </div>

                    @if ($invitation->team)
                        <div class="relative mt-3 flex items-center gap-3 pl-12">
                            {{-- Tree-branch curve. --}}
                            <span
                                class="absolute left-[18px] top-0 h-[18px] w-3 rounded-bl-xl border-b border-l border-border"></span>

                            <div
                                class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-full bg-muted text-sm font-medium text-muted-foreground">
                                @if ($invitation->team->logo_url)
                                    <img src="{{ $invitation->team->logo_url }}" alt="" class="size-full object-cover">
                                @else
                                    {{ $invitation->team->initials() }}
                                @endif
                            </div>

                            <div class="flex min-w-0 flex-1 flex-col">
                                <p class="truncate text-sm font-medium text-foreground">{{ $invitation->team->name }}</p>
                                <p class="text-xs text-muted-foreground">{{ $this->teamMemberCount }} {{ Str::plural(__('public/invitations/onboarding.member'), $this->teamMemberCount) }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <x-ui.button wire:click="continueFromWelcome" variant="secondary" size="lg" class="w-full"
                             :label="__('public/invitations/onboarding.continue')"/>
            </div>
        @elseif ($step === 'password')
            <x-domain.auth.step :title="__('public/invitations/onboarding.password_step_title')"
                                wire:key="step-password">
                <div class="flex flex-col items-center gap-0.5 w-full text-center">
                    <p class="text-muted-foreground text-sm">{{ __('public/invitations/onboarding.creating_account_for') }}</p>
                    <p class="text-foreground text-sm font-medium break-all">{{ $invitation->email }}</p>
                </div>

                <form wire:submit="continueFromPassword" class="flex flex-col gap-4 w-full">
                    <x-ui.field>
                        <x-ui.input type="password" wire:model="form.password" name="password"
                                    autocomplete="new-password" required/>
                        <x-ui.field.error :content="$errors->first('form.password')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full"
                                 wire:target="continueFromPassword"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove
                              wire:target="continueFromPassword">{{ __('public/invitations/onboarding.continue') }}</span>
                        <span wire:loading
                              wire:target="continueFromPassword">{{ __('public/invitations/onboarding.continuing') }}</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToWelcome" variant="link" size="lg"
                                 :label="__('public/invitations/onboarding.back_link')"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @elseif ($step === 'profile')
            <x-domain.auth.step :title="__('public/invitations/onboarding.profile_step_title')" wire:key="step-profile">
                <form
                    wire:submit="finish"
                    class="flex flex-col gap-4 w-full"
                    x-data="{
                    name: @js($form->name),
                    initials() {
                        const parts = this.name.trim().split(/\s+/).filter(Boolean).map(p => p[0]);
                        const initials = parts.join('').toUpperCase();

                        return initials.length > 1 ? initials[0] + initials[initials.length - 1] : initials;
                    },
                }"
                >
                    <x-ui.field>
                        <x-ui.field.label :content="__('public/invitations/onboarding.avatar_label')"/>

                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-full border border-border bg-muted text-xs font-medium text-muted-foreground">
                                @if ($form->avatar && $form->avatar->isPreviewable())
                                    <img src="{{ $form->avatar->temporaryUrl() }}" alt=""
                                         class="size-full object-cover">
                                @else
                                    <span x-text="initials()"></span>
                                @endif
                            </div>

                            <input type="file" wire:model="form.avatar" accept="image/*" class="hidden"
                                   x-ref="avatarInput">
                            <x-ui.button type="button" variant="outline" size="sm"
                                         :label="__('public/invitations/onboarding.choose_photo')"
                                         x-on:click="$refs.avatarInput.click()"/>
                        </div>

                        <x-ui.field.error :content="$errors->first('form.avatar')"/>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field.label :content="__('public/invitations/onboarding.full_name_label')"/>
                        <x-ui.input wire:model="form.name" x-model="name" name="name" autocomplete="name" required/>
                        <x-ui.field.error :content="$errors->first('form.name')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="finish"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove
                              wire:target="finish">{{ __('public/invitations/onboarding.continue') }}</span>
                        <span wire:loading
                              wire:target="finish">{{ __('public/invitations/onboarding.finishing_up') }}</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToPassword" variant="link" size="lg"
                                 :label="__('public/invitations/onboarding.back_link')"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @endif
    </div>
</div>
