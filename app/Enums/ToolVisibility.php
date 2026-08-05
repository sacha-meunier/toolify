<?php

namespace App\Enums;

enum ToolVisibility: string
{
    case Public = 'public';
    case Unlisted = 'unlisted';
    case Private = 'private';

    public static function default(): self
    {
        return self::Private;
    }

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public',
            self::Unlisted => 'Unlisted',
            self::Private => 'Private',
        };
    }
}
