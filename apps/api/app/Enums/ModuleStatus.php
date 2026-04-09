<?php

declare(strict_types=1);

namespace App\Enums;

enum ModuleStatus: string
{
    case ACTIVE = 'active';
    case HIDDEN = 'hidden';
    case EXPERIMENTAL = 'experimental';
    case DISABLED = 'disabled';
}
