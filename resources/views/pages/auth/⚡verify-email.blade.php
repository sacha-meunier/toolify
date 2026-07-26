<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public function checkVerification(): void
    {
        if (!Auth::user()->fresh()->hasVerifiedEmail()) {
            return;
        }

        // The user confirmed their email from another tab/device: catch up this tab instead
        // of leaving it stuck on the "check your email" screen.
        $this->redirect(session()->pull('url.intended', config('fortify.home')), navigate: true);
    }
}
?>

<div
    class="flex flex-col items-center gap-6"
    x-data
    x-init="
        let interval = null;
        const start = () => { stop(); $wire.checkVerification(); interval = setInterval(() => $wire.checkVerification(), 3000); };
        const stop = () => clearInterval(interval);
        const onVisibilityChange = () => (document.hidden ? stop() : start());

        if (!document.hidden) start();
        document.addEventListener('visibilitychange', onVisibilityChange);
        document.addEventListener('livewire:navigate', () => {
            stop();
            document.removeEventListener('visibilitychange', onVisibilityChange);
        }, { once: true });
    "
>
    {{-- Brand : to be replaced later --}}
    <x-ui.icon.command class="size-12 block"/>

    <x-domain.auth.step title="Check your email">
        <div class="flex flex-col gap-4 w-full">
            <p class="text-muted-foreground text-sm text-center">
                We sent a verification link to <span
                    class="text-foreground font-medium">{{ auth()->user()->email }}</span>.
                Click it to finish setting up your account.
            </p>

            @if (session('status') === 'verification-link-sent')
                <p class="text-sm text-center text-foreground">A new verification link has been sent.</p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-ui.button type="submit" variant="secondary" size="lg" label="Resend email" class="w-full"/>
            </form>
        </div>

        <x-slot:footer>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <div class="flex items-center justify-center gap-2">
                    <p class="text-muted-foreground font-medium text-sm">Not you ?</p>
                    <x-ui.button type="submit" variant="link" size="lg" label="Log out"/>
                </div>
            </form>
        </x-slot:footer>
    </x-domain.auth.step>
</div>
