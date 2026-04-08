<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Enums;

use App\Domain\Dashboard\Models\HomepageItem;

enum MorphTypes: string
{
    case DASHBOARD_HOMEPAGE_ITEM = HomepageItem::class;
}
