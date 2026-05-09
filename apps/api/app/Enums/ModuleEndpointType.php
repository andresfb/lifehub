<?php

declare(strict_types=1);

namespace App\Enums;

enum ModuleEndpointType: string
{
    case COMMAND = 'command';
    case ACTION = 'action';
}
