<?php

namespace App\Enums;

enum ToolHeadcount: string
{
    case OneToTen = '1-10';
    case ElevenToFifty = '11-50';
    case FiftyOneToOneHundred = '51-100';
    case OneHundredOneToFiveHundred = '101-500';
    case MoreThanFiveHundred = '500+';

    public function label(): string
    {
        return match ($this) {
            self::OneToTen => '1-10',
            self::ElevenToFifty => '11-50',
            self::FiftyOneToOneHundred => '51-100',
            self::OneHundredOneToFiveHundred => '101-500',
            self::MoreThanFiveHundred => '500+',
        };
    }
}
