<?php

namespace App\Enums;

enum ModuleEndpointType: string
{
    case COMMAND = 'command';
    case ACTION = 'action';
}
