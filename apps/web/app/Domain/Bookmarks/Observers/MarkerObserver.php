<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Observers;

use App\Domain\Bookmarks\Jobs\MarkerMutatorJob;
use App\Domain\Bookmarks\Jobs\SearchDocumentDeletedJob;
use App\Domain\Bookmarks\Jobs\SearchDocumentUpdatedJob;
use App\Domain\Bookmarks\Models\Marker;
use Illuminate\Support\Facades\Bus;

final readonly class MarkerObserver
{
    public function created(Marker $marker): void
    {
        Bus::chain([
            new MarkerMutatorJob($marker->id),
            new SearchDocumentUpdatedJob($marker->id),
        ])->dispatch();
    }

    public function updated(Marker $marker): void
    {
        SearchDocumentUpdatedJob::dispatch($marker->id);
    }

    public function deleted(Marker $marker): void
    {
        SearchDocumentDeletedJob::dispatch($marker->id);
    }
}
