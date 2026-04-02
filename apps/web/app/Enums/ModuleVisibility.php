<?php

declare(strict_types=1);

namespace App\Enums;

enum ModuleVisibility: string
{
    case VISIBLE = 'visible';
    case HIDDEN = 'hidden';
    case INTERNAL = 'internal';
}
