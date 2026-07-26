<?php

use App\Livewire\Traits\LastAuthMethodUsed;
use App\Livewire\Traits\StepsThroughAuthMethods;
use App\Livewire\Forms\Auth\RegisterForm;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Livewire\Component;

new class extends Component {
    use LastAuthMethodUsed, StepsThroughAuthMethods;

    public RegisterForm $form;

    public function register(CreatesNewUsers $creator): void
    {
        $this->form->validate();

        // The design uses a single password field (with a reveal toggle) instead of a confirmation field,
        // so we mirror the value into password_confirmation to
        // satisfy Fortitfy's CreateNewUser's shared "confirmed" rule without altering that action.
        $user = $creator->create([
            'name' => Str::before($this->form->email, '@'),
            'email' => $this->form->email,
            'password' => $this->form->password,
            'password_confirmation' => $this->form->password,
        ]);

        Auth::login($user);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        // Remembered by `LastAuthMethodUsed` to highlight "You used X last time" on the next visit.
        Cookie::queue('last_auth_method', 'email', 60 * 24 * 365);

        // The event `Registered()` sends the verification email listened by `SendEmailVerificationNotification`.
        // If there's a email provider hiccup (e.g too many email requests on free provider plan) :
        // the user isn't stuck on a 500 with an existing account and they can ask for resend from the email verification screen.
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            report($e);
        }

        $this->redirect(route('verification.notice'), navigate: true);
    }
}
?>

<div class="flex flex-col gap-6">
    {{-- Brand : to be replaced later --}}
    <x-ui.icon.command class="size-12 block self-center"/>

    <div wire:transition="content" class="w-full">
        @if ($step === 1)
            <div class="flex flex-col gap-6" wire:key="step-1">
                <div>
                    <h1 class="text-foreground text-center font-semibold text-lg">Sign up to Toolify</h1>
                </div>

                <div class="flex flex-col gap-4" role="group" aria-label="Sign up method">
                    @foreach ($this->authMethods as $method => $config)
                        <x-domain.auth.method wire:key="method-{{ $method }}" :method="$method" :config="$config"
                                              :primary="$loop->first"
                                              :is-last-auth-method-used="$lastAuthMethod === $method"/>
                    @endforeach
                </div>

                <div class="flex items-center justify-center gap-2">
                    <p class="text-muted-foreground font-medium text-sm">Already have an account ?</p>
                    <x-ui.button variant="link" type="a" href="{{ route('login') }}" size="lg" label="Log in"/>
                </div>
            </div>
        @elseif ($step === 2)
            <x-domain.auth.step title="What's your email address?" wire:key="step-2">
                <form wire:submit="submitEmail" class="flex flex-col gap-4 w-full">
                    <x-ui.field>
                        <x-ui.input type="email" wire:model="form.email" name="email" placeholder="johndoe@gmail.com"
                                    autocomplete="email" required/>
                        <x-ui.field.error :content="$errors->first('form.email')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="submitEmail"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="submitEmail">Continue</span>
                        <span wire:loading wire:target="submitEmail">Continuing…</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToMethods" variant="link" size="lg" label="Back to sign up methods"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @elseif ($step === 3)
            <x-domain.auth.step title="What's your password?" wire:key="step-3">
                <form wire:submit="register" class="flex flex-col gap-4 w-full">
                    <x-ui.field>
                        <x-ui.input type="password" wire:model="form.password" name="password"
                                    autocomplete="new-password" required/>
                        <x-ui.field.error :content="$errors->first('form.password')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="register"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="register">Continue</span>
                        <span wire:loading wire:target="register">Creating your account…</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToEmail" variant="link" size="lg" label="Back to sign up email"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @endif
    </div>
</div>
