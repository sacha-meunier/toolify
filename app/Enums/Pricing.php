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
        return match ($this) {
            self::Free => 'Free',
            self::Freemium => 'Freemium',
            self::Paid => 'Paid',
            self::Subscription => 'Subscription',
        };
    }
}
