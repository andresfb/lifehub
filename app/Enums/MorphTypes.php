<?php

namespace App\Enums;

use App\Models\Reminder;

enum MorphTypes: string
{
    case CORE_REMINDER = Reminder::class;
}
