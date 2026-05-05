<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Enums;

enum PinStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;
    case ALL = -1;
}
