<?php

declare(strict_types=1);

namespace App\Domain\Core\Observers;

use App\Domain\Core\Jobs\HomepageItemDeletedJob;
use App\Domain\Core\Jobs\HomepageItemSavedJob;
use App\Domain\Core\Models\HomepageItem;

final class HomepageItemObserver
{
    public function creating(HomepageItem $item): void
    {
        if (filled($item->order)) {
            return;
        }

        $item->order = HomepageItem::query()->max('order') + 1;
    }

    public function saved(HomepageItem $item): void
    {
        HomepageItemSavedJob::dispatch($item->id);
    }

    public function deleted(HomepageItem $item): void
    {
        HomepageItemDeletedJob::dispatch($item->id);
    }
}
