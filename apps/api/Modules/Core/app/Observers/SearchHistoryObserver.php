<?php

declare(strict_types=1);

namespace Modules\Core\Observers;

use Spatie\ResponseCache\Facades\ResponseCache;

final class SearchHistoryObserver
{
    public function saved(): void
    {
        ResponseCache::clear(['search-terms']);
    }
}
