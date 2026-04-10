<?php

declare(strict_types=1);

namespace Modules\Core\Enums;

use Modules\Core\Models\Reminder;

enum MorphTypes: string
{
    case CORE_REMINDER = Reminder::class;
}
