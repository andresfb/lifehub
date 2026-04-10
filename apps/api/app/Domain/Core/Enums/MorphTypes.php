<?php

declare(strict_types=1);

namespace App\Domain\Core\Enums;

use App\Domain\Core\Models\Reminder;

enum MorphTypes: string
{
    case CORE_REMINDER = Reminder::class;
}
