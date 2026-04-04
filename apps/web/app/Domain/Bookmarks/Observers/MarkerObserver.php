<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Observers;

use App\Domain\Bookmarks\Jobs\MarkerMutatorAIJob;
use App\Domain\Bookmarks\Jobs\SearchDocumentDeletedJob;
use App\Domain\Bookmarks\Jobs\SearchDocumentUpdatedJob;
use App\Domain\Bookmarks\Models\Marker;
use Illuminate\Support\Facades\Cache;

final readonly class MarkerObserver
{
    public function creating(Marker $marker): void
    {
        $marker->title = trim($marker->title);
        $marker->url = trim($marker->url);
        $marker->hash = md5($marker->url);
    }

    public function created(Marker $marker): void
    {
        MarkerMutatorAIJob::dispatch($marker->id);
    }

    public function updated(Marker $marker): void
    {
        SearchDocumentUpdatedJob::dispatch($marker->id);
    }

    public function saved(Marker $marker): void
    {
        Cache::tags('markers')->flush();
    }

    public function deleted(Marker $marker): void
    {
        SearchDocumentDeletedJob::dispatch($marker->id);
    }
}
