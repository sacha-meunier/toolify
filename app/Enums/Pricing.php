<?php

namespace App\Enums;

enum Pricing: string
{
    case Free = 'free';
    case Freemium = 'freemium';
    case Paid = 'paid';
    case Subscription = 'subscription';

    public function label(): string
    {
        return __('enums/pricing.'.$this->value);
    }
}
