<?php

use App\Livewire\Traits\LastAuthMethodUsed;
use App\Livewire\Traits\StepsThroughAuthMethods;
use App\Livewire\Traits\ThrottlesLoginAttempts;
use App\Livewire\Forms\Auth\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component {
    use LastAuthMethodUsed, StepsThroughAuthMethods, ThrottlesLoginAttempts;

    public LoginForm $form;

    public function login(): void
    {
        $this->form->validate();

        $this->ensureLoginIsNotRateLimited($this->form->email);

        if (!Auth::attempt(['email' => $this->form->email, 'password' => $this->form->password])) {
            $this->incrementLoginAttempts($this->form->email);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->clearLoginAttempts($this->form->email);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        // Remembered by `LastAuthMethodUsed` to highlight "You used X last time" on the next visit.
        // Last auth method is stored for a year.
        Cookie::queue('last_auth_method', 'email', 60 * 24 * 365);

        $user = Auth::user();

        // Stop here and send unverified users to the verification screen instead of letting
        // them through to the app.
        if (!$user->hasVerifiedEmail()) {
            $this->redirect(route('verification.notice'), navigate: true);

            return;
        }

        // When visiting an url while not logged or verified,
        // the user is redirected to the login page,
        // once signed in, if authorized, redirects to the intended url,
        // otherwise fall back to configured home.
        // `pull()` reads and removes `url.intended` from the session.
        $this->redirect(session()->pull('url.intended', config('fortify.home')), navigate: true);
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
                    <h1 class="text-foreground text-center font-semibold text-lg">{{ __('auth/login.heading') }}</h1>
                </div>

                <div class="flex flex-col gap-4" role="group" aria-label="{{ __('auth/login.method_group_label') }}">
                    @foreach ($this->authMethods as $method => $config)
                        <x-domain.auth.method wire:key="method-{{ $method }}" :method="$method" :config="$config"
                                              :primary="$loop->first"
                                              :is-last-auth-method-used="$lastAuthMethod === $method"/>
                    @endforeach
                </div>

                <div class="flex items-center justify-center gap-2">
                    <p class="text-muted-foreground font-medium text-sm">{{ __('auth/login.no_account_prompt') }}</p>
                    <x-ui.button variant="link" type="a" href="{{ route('register') }}" size="lg" :label="__('auth/login.sign_up_link')"/>
                </div>
            </div>
        @elseif ($step === 2)
            <x-domain.auth.step :title="__('auth/login.heading')" wire:key="step-2">
                <form wire:submit="login" class="flex flex-col gap-4 w-full">
                    <x-ui.field>
                        <x-ui.field.label :content="__('auth/login.email_label')" for="email"/>
                        <x-ui.input type="email" wire:model="form.email" name="email" id="email"
                                    :placeholder="__('auth/login.email_placeholder')" autocomplete="email" required/>
                        <x-ui.field.error :content="$errors->first('form.email') ?: $errors->first('email')"/>
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field.label :content="__('auth/login.password_label')" for="password"/>
                        <x-ui.input type="password" wire:model="form.password" name="password" id="password"
                                    autocomplete="current-password" required/>
                        <x-ui.field.error :content="$errors->first('form.password')"/>
                    </x-ui.field>

                    <x-ui.button type="submit" variant="secondary" size="lg" class="w-full" wire:target="login"
                                 wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="login">{{ __('auth/login.continue_button') }}</span>
                        <span wire:loading wire:target="login">{{ __('auth/login.logging_in_button') }}</span>
                    </x-ui.button>
                </form>

                <x-slot:footer>
                    <x-ui.button wire:click="backToMethods" variant="link" size="lg" :label="__('auth/login.back_to_methods_link')"/>
                    <x-ui.button variant="link" type="a" href="{{ route('password.request') }}" size="lg"
                                 :label="__('auth/login.forgot_password_link')"/>
                </x-slot:footer>
            </x-domain.auth.step>
        @endif
    </div>
</div>
