<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\Transition;

/**
 * Transition types & actions between the steps during the login/signup wizard
 */
trait StepsThroughAuthMethods
{
    public int $step = 1;

    // Used in step #1 during Login/Signup wizard
    #[Transition(type: 'forward')]
    public function continueWithEmail(): void
    {
        $this->step = 2;
    }

    // Used in step #2 during Login/Signup wizard
    #[Transition(type: 'backward')]
    public function backToMethods(): void
    {
        $this->step = 1;
    }

    // Used in step #3 during Signup wizard
    #[Transition(type: 'forward')]
    public function submitEmail(): void
    {
        $this->form->validateOnly('email');

        $this->step = 3;
    }

    // Used in step #4 during Signup wizard
    #[Transition(type: 'backward')]
    public function backToEmail(): void
    {
        $this->step = 2;
    }
}
