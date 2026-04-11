<?php

declare(strict_types=1);

namespace Modules\Dashboard\Observers;

use Modules\Dashboard\Jobs\HomepageItemDeletedJob;
use Modules\Dashboard\Jobs\HomepageItemSavedJob;
use Modules\Dashboard\Models\HomepageItem;
use Spatie\ResponseCache\Facades\ResponseCache;

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

        ResponseCache::clear(['dashboard']);
    }

    public function deleted(HomepageItem $item): void
    {
        HomepageItemDeletedJob::dispatch($item->id);

        ResponseCache::clear(['dashboard']);
    }
}
