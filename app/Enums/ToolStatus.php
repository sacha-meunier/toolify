<?php

namespace App\Enums;

enum ToolStatus: string
{
    case Active = 'active';
    case ShutDown = 'shut_down';
    case Acquired = 'acquired';

    public function label(): string
    {
        return __('enums/tool-status.'.$this->value);
    }
}
