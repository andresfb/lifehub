<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Observers;

use App\Domain\Bookmarks\Jobs\MarkerMutatorAIJob;
use App\Domain\Bookmarks\Jobs\MarkerDeletedJob;
use App\Domain\Bookmarks\Jobs\MarkerUpdatedJob;
use App\Domain\Bookmarks\Models\Marker;
use Illuminate\Support\Facades\Cache;

final readonly class MarkerObserver
{
    public function creating(Marker $marker): void
    {
        $marker->title = trim($marker->title);
        $marker->url = trim($marker->url);
        $marker->hash = Marker::getHash($marker->url);
    }

    public function created(Marker $marker): void
    {
        MarkerMutatorAIJob::dispatch($marker->id);
    }

    public function updated(Marker $marker): void
    {
        MarkerUpdatedJob::dispatch($marker->id);
    }

    public function saved(): void
    {
        Cache::tags('markers')->flush();
    }

    public function deleted(Marker $marker): void
    {
        MarkerDeletedJob::dispatch($marker->id);
    }
}
