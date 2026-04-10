<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Actions;

use App\Domain\Bookmarks\Dtos\BulkMarkerImportItem;
use App\Domain\Bookmarks\Jobs\BulkMarkerImportJob;

final class BulkMarkerImportAction
{
    public function handle(array $markers): void
    {
        $list = collect($markers)->map(fn (array $marker): BulkMarkerImportItem => BulkMarkerImportItem::from($marker));

        BulkMarkerImportJob::dispatch($list);
    }
}
