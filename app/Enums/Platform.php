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
        return match ($this) {
            self::Web => 'Web',
            self::Ios => 'iOS',
            self::Android => 'Android',
            self::Macos => 'macOS',
            self::Windows => 'Windows',
            self::Linux => 'Linux',
        };
    }
}
