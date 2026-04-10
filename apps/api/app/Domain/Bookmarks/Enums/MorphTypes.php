<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Enums;

use App\Domain\Bookmarks\Models\Marker;

enum MorphTypes: string
{
    case BOOKMARKS_MARKER = Marker::class;
}
