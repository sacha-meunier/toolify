<?php

use App\Livewire\Forms\Onboarding\OnboardingForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Transition;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.auth')]
class extends Component {
    use WithFileUploads;

    public string $step = 'profile'; // profile | workspace | create | join

    public OnboardingForm $form;

    public function mount(): void
    {
        if (auth()->user()->onboarded_at) {
            $this->redirect(route('search'), navigate: true);

            return;
        }

        $this->form->setUser(auth()->user());
    }

    #[Transition(type: 'forward')]
    public function continueFromProfile(): void
    {
        $this->form->saveProfile();

        $this->step = 'workspace';
    }

    #[Transition(type: 'backward')]
    public function backToProfile(): void
    {
        $this->step = 'profile';
    }

    #[Transition(type: 'forward')]
    public function continueFromWorkspace(): void
    {
        $this->step = $this->form->workspaceChoice === 'join' ? 'join' : 'create';
    }

    #[Transition(type: 'backward')]
    public function backToWorkspace(): void
    {
        $this->step = 'workspace';
    }

    public function finishCreate(): void
    {
        $this->form->createWorkspace();

        $this->finishOnboarding();
    }

    public function finishJoin(): void
    {
        if (! $this->form->joinWorkspace()) {
            return;
        }

        $this->finishOnboarding();
    }

    private function finishOnboarding(): void
    {
        auth()->user()->forceFill(['onboarded_at' => now()])->save();

        $this->redirect(route('search'), navigate: true);
    }
};
?>

<div class="flex flex-col gap-6">
    {{-- Brand : to be replaced later --}}
    <x-ui.icon.command class="size-12 block self-center"/>

    <div wire:transition="content" class="w-full">
        @if ($step === 'profile')
            <div class="flex flex-col gap-6" wire:key="step-profile">
                <div class="flex flex-col gap-1">
                    <h1 class="text-foreground text-center font-semibold text-lg">{{ __('app/onboarding/index.profile_heading') }}</h1>
                    <p class="text-muted-foreground text-sm text-center">{{ __('app/onboarding/index.profile_description') }}</p>
                </div>

                <form
                    wire:submit="continueFromProfile"
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
                        <x-ui.field.label :content="__('app/onboarding/index.avatar_label')"/>

                        <div class="flex items-center gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-full border border-border bg-muted text-xs font-medium text-muted-foreground">
                                @if ($form->avatar && $form->avatar->isPreviewable())
                                    <img src="{{ $form->avatar->temporaryUrl() }}" alt="" class="size-full object-cover">
                                @elseif ($form->user->avatar_url)
                                    <img src="{{ $form->user->avatar_url }}" alt="" class="size-full object-cover">
                                @else
                                    <span x-text="initials()">{{ $form->user->initials() }}</span>
                                @endif
                            </div>

                            <input type="file" wire:model="form.avatar" accept="image/*" class="hidden" x-ref="avatarInput">
                            <x-ui.button type="button" variant="outline" size="sm" :label="__('app/onboarding/index.choose_photo')" x-on:click="$refs.avatarInput.click()"/>
                        </div>

                        <x-ui.field.error :content="$errors->first('form.avatar')"/>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field.label :content="__('app/onboarding/index.fullname_label')"/>
                        <x-ui.input wire:model="form.name" x-model="name" name="name" autocomplete="name" required/>
                        <x-ui.field.error :content="$errors->first('form.name')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="continueFromProfile"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="continueFromProfile">{{ __('app/onboarding/index.continue') }}</span>
                        <span wire:loading wire:target="continueFromProfile">{{ __('app/onboarding/index.continuing') }}</span>
                    </x-ui.button>
                </form>
            </div>
        @elseif ($step === 'workspace')
            <div class="flex flex-col gap-6" wire:key="step-workspace">
                <div class="flex flex-col gap-1">
                    <h1 class="text-foreground text-center font-semibold text-lg">{{ __('app/onboarding/index.workspace_heading') }}</h1>
                    <p class="text-muted-foreground text-sm text-center">{{ __('app/onboarding/index.workspace_description') }}</p>
                </div>

                <div class="flex flex-col gap-3" role="radiogroup" aria-label="{{ __('app/onboarding/index.workspace_choice_group_label') }}">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-border p-4 has-[:checked]:border-foreground">
                        <div class="flex flex-1 flex-col gap-0.5">
                            <p class="text-sm font-medium text-foreground">{{ __('app/onboarding/index.create_option_label') }}</p>
                            <p class="text-sm text-muted-foreground">{{ __('app/onboarding/index.create_option_description') }}</p>
                        </div>
                        <input type="radio" wire:model="form.workspaceChoice" value="create" class="mt-0.5 size-4 accent-foreground">
                    </label>

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-border p-4 has-[:checked]:border-foreground">
                        <div class="flex flex-1 flex-col gap-0.5">
                            <p class="text-sm font-medium text-foreground">{{ __('app/onboarding/index.join_option_label') }}</p>
                            <p class="text-sm text-muted-foreground">{{ __('app/onboarding/index.join_option_description') }}</p>
                        </div>
                        <input type="radio" wire:model="form.workspaceChoice" value="join" class="mt-0.5 size-4 accent-foreground">
                    </label>
                </div>

                <x-ui.button wire:click="continueFromWorkspace" variant="secondary" size="lg" class="w-full" :label="__('app/onboarding/index.continue')"/>

                <div class="flex items-center justify-center">
                    <x-ui.button wire:click="backToProfile" variant="link" size="lg" :label="__('app/onboarding/index.back_link')"/>
                </div>
            </div>
        @elseif ($step === 'create')
            <x-domain.auth.step :title="__('app/onboarding/index.create_step_title')" wire:key="step-create">
                <form
                    wire:submit="finishCreate"
                    class="flex flex-col gap-4 w-full"
                    x-data="{
                        name: @js($form->workspaceName),
                        initials() {
                            const parts = this.name.trim().split(/\s+/).filter(Boolean).map(p => p[0]);
                            const initials = parts.join('').toUpperCase();

                            return initials.length > 1 ? initials[0] + initials[initials.length - 1] : initials;
                        },
                    }"
                >
                    <x-ui.field>
                        <x-ui.field.label :content="__('app/onboarding/index.workspace_avatar_label')"/>

                        <div class="flex items-center gap-3">
                            <div class="flex size-9 shrink-0 items-center justify-center overflow-clip rounded-full border border-border bg-muted text-xs font-medium text-muted-foreground">
                                @if ($form->workspaceLogo && $form->workspaceLogo->isPreviewable())
                                    <img src="{{ $form->workspaceLogo->temporaryUrl() }}" alt="" class="size-full object-cover">
                                @else
                                    <span x-text="initials()" x-show="name.trim() !== ''"></span>
                                    <x-ui.icon.layer class="size-4" x-show="name.trim() === ''"/>
                                @endif
                            </div>

                            <input type="file" wire:model="form.workspaceLogo" accept="image/*" class="hidden" x-ref="workspaceLogoInput">
                            <x-ui.button type="button" variant="outline" size="sm" :label="__('app/onboarding/index.choose_photo')" x-on:click="$refs.workspaceLogoInput.click()"/>
                        </div>

                        <x-ui.field.error :content="$errors->first('form.workspaceLogo')"/>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field.label :content="__('app/onboarding/index.workspace_name_label')"/>
                        <x-ui.input wire:model="form.workspaceName" x-model="name" name="workspaceName" required/>
                        <x-ui.field.error :content="$errors->first('form.workspaceName')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="finishCreate"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="finishCreate">{{ __('app/onboarding/index.continue') }}</span>
                        <span wire:loading wire:target="finishCreate">{{ __('app/onboarding/index.finishing_up') }}</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToWorkspace" variant="link" size="lg" :label="__('app/onboarding/index.back_link')"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @elseif ($step === 'join')
            <x-domain.auth.step :title="__('app/onboarding/index.join_step_title')" wire:key="step-join">
                <p class="text-muted-foreground text-sm text-center w-full">{{ __('app/onboarding/index.join_step_description') }}</p>

                <form wire:submit="finishJoin" class="flex flex-col gap-4 w-full">
                    <x-ui.field>
                        <x-ui.field.label :content="__('app/onboarding/index.invite_code_label')"/>
                        <x-ui.input wire:model="form.inviteCode" name="inviteCode" :placeholder="__('app/onboarding/index.invite_code_placeholder')" required/>
                        <x-ui.field.error :content="$errors->first('form.inviteCode')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="finishJoin"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="finishJoin">{{ __('app/onboarding/index.continue') }}</span>
                        <span wire:loading wire:target="finishJoin">{{ __('app/onboarding/index.finishing_up') }}</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToWorkspace" variant="link" size="lg" :label="__('app/onboarding/index.back_link')"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @endif
    </div>
</div>
