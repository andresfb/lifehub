<?php

declare(strict_types=1);

namespace Modules\Dashboard\Enums;

use Modules\Dashboard\Models\HomepageItem;

enum MorphTypes: string
{
    case DASHBOARD_HOMEPAGE_ITEM = HomepageItem::class;
}
