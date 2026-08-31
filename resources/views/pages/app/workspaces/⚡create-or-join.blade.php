<?php

use App\Livewire\Forms\Workspaces\CreateOrJoinWorkspaceForm;
use App\Models\Workspace;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Transition;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::shells.wizard')]
class extends Component {
    use WithFileUploads;

    public string $step = 'choice'; // choice | create | join

    public CreateOrJoinWorkspaceForm $form;

    public function mount(): void
    {
        $this->form->setUser(auth()->user());

        // Read by `layouts::shells.wizard` for its back button, so cancelling this flow
        // returns to wherever it was opened from instead of a fixed destination.
        session(['workspace_wizard_back_href' => url()->previous()]);
    }

    #[Transition(type: 'forward')]
    public function continueFromChoice(): void
    {
        $this->step = $this->form->workspaceChoice === 'join' ? 'join' : 'create';
    }

    #[Transition(type: 'backward')]
    public function backToChoice(): void
    {
        $this->step = 'choice';
    }

    public function finishCreate(): void
    {
        $this->switchToAndLeave($this->form->createWorkspace());
    }

    public function finishJoin(): void
    {
        $workspace = $this->form->joinWorkspace();

        if (! $workspace) {
            return;
        }

        $this->switchToAndLeave($workspace);
    }

    /**
     * Make the new/joined workspace the active one, same as the sidebar's workspace switcher,
     * then send the user to the discovery page.
     */
    private function switchToAndLeave(Workspace $workspace): void
    {
        session(['current_workspace_id' => $workspace->id]);

        $this->redirect(route('discovery'), navigate: true);
    }
};
?>

<div class="flex flex-col gap-6">
    <div wire:transition="content" class="w-full">
        @if ($step === 'choice')
            <div class="flex flex-col gap-6" wire:key="step-choice">
                <div class="flex flex-col gap-1">
                    <h1 class="text-foreground text-center font-semibold text-lg">{{ __('app/workspaces/create-or-join.choice_heading') }}</h1>
                    <p class="text-muted-foreground text-sm text-center">{{ __('app/workspaces/create-or-join.choice_description') }}</p>
                </div>

                <div class="flex flex-col gap-3" role="radiogroup" aria-label="{{ __('app/workspaces/create-or-join.choice_group_label') }}">
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-border bg-pane p-4 hover:bg-card has-[:checked]:border-foreground has-[:checked]:bg-secondary has-[:checked]:hover:bg-secondary has-[:focus-visible]:ring-3 has-[:focus-visible]:ring-ring/50 has-[:checked]:has-[:focus-visible]:ring-4">
                        <div class="flex flex-1 flex-col gap-0.5">
                            <p class="text-sm font-medium text-foreground">{{ __('app/workspaces/create-or-join.create_option_label') }}</p>
                            <p class="text-sm text-muted-foreground">{{ __('app/workspaces/create-or-join.create_option_description') }}</p>
                        </div>
                        <span class="relative mt-0.5 flex size-4 shrink-0 items-center justify-center">
                            <input type="radio" wire:model="form.workspaceChoice" value="create" class="peer sr-only">
                            <span class="size-4 rounded-full border border-muted-foreground peer-checked:border-foreground"></span>
                            <span class="absolute size-1.5 rounded-full bg-foreground opacity-0 peer-checked:opacity-100"></span>
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-border bg-pane p-4 hover:bg-card has-[:checked]:border-foreground has-[:checked]:bg-secondary has-[:checked]:hover:bg-secondary has-[:focus-visible]:ring-3 has-[:focus-visible]:ring-ring/50 has-[:checked]:has-[:focus-visible]:ring-4">
                        <div class="flex flex-1 flex-col gap-0.5">
                            <p class="text-sm font-medium text-foreground">{{ __('app/workspaces/create-or-join.join_option_label') }}</p>
                            <p class="text-sm text-muted-foreground">{{ __('app/workspaces/create-or-join.join_option_description') }}</p>
                        </div>
                        <span class="relative mt-0.5 flex size-4 shrink-0 items-center justify-center">
                            <input type="radio" wire:model="form.workspaceChoice" value="join" class="peer sr-only">
                            <span class="size-4 rounded-full border border-muted-foreground peer-checked:border-foreground"></span>
                            <span class="absolute size-1.5 rounded-full bg-foreground opacity-0 peer-checked:opacity-100"></span>
                        </span>
                    </label>
                </div>

                <x-ui.button wire:click="continueFromChoice" variant="secondary" size="lg" class="w-full" :label="__('app/workspaces/create-or-join.continue')"/>
            </div>
        @elseif ($step === 'create')
            <x-domain.auth.step :title="__('app/workspaces/create-or-join.create_step_title')" wire:key="step-create">
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
                        <x-ui.field.label :content="__('app/workspaces/create-or-join.workspace_avatar_label')"/>

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
                            <x-ui.button type="button" variant="outline" size="sm" :label="__('app/workspaces/create-or-join.choose_photo')" x-on:click="$refs.workspaceLogoInput.click()"/>
                        </div>

                        <x-ui.field.error :content="$errors->first('form.workspaceLogo')"/>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field.label :content="__('app/workspaces/create-or-join.workspace_name_label')"/>
                        <x-ui.input wire:model="form.workspaceName" x-model="name" name="workspaceName" :placeholder="__('app/workspaces/create-or-join.workspace_name_placeholder')" required/>
                        <x-ui.field.error :content="$errors->first('form.workspaceName')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="finishCreate"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="finishCreate">{{ __('app/workspaces/create-or-join.continue') }}</span>
                        <span wire:loading wire:target="finishCreate">{{ __('app/workspaces/create-or-join.finishing_up') }}</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToChoice" variant="link" size="lg" :label="__('app/workspaces/create-or-join.back_link')"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @elseif ($step === 'join')
            <x-domain.auth.step :title="__('app/workspaces/create-or-join.join_step_title')" wire:key="step-join">
                <p class="text-muted-foreground text-sm text-center w-full">{{ __('app/workspaces/create-or-join.join_step_description') }}</p>

                <form wire:submit="finishJoin" class="flex flex-col gap-4 w-full">
                    <x-ui.field>
                        <x-ui.field.label :content="__('app/workspaces/create-or-join.invite_code_label')"/>
                        <x-ui.input wire:model="form.inviteCode" name="inviteCode" :placeholder="__('app/workspaces/create-or-join.invite_code_placeholder')" required/>
                        <x-ui.field.error :content="$errors->first('form.inviteCode')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="finishJoin"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="finishJoin">{{ __('app/workspaces/create-or-join.continue') }}</span>
                        <span wire:loading wire:target="finishJoin">{{ __('app/workspaces/create-or-join.finishing_up') }}</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToChoice" variant="link" size="lg" :label="__('app/workspaces/create-or-join.back_link')"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @endif
    </div>
</div>
