<?php

declare(strict_types=1);

namespace Modules\Dashboard\Observers;

use Modules\Dashboard\Models\HomepageItem;

final class HomepageItemObserver
{
    public function creating(HomepageItem $item): void
    {
        if (filled($item->order)) {
            return;
        }

        $item->order = HomepageItem::query()->max('order') + 1;
    }
}
