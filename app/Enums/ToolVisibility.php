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
        return __('enums/tool-visibility.'.$this->value);
    }
}
