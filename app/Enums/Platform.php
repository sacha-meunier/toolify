<?php

namespace App\Enums;

enum Platform: string
{
    case Web = 'web';
    case Ios = 'ios';
    case Android = 'android';
    case Macos = 'macos';
    case Windows = 'windows';
    case Linux = 'linux';

    public function label(): string
    {
        return __('enums/platform.'.$this->value);
    }
}
