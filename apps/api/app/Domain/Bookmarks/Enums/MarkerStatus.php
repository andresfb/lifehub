<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Enums;

enum MarkerStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
    case HIDDEN = 'hidden';
}
