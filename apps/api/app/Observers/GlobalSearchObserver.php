<?php

declare(strict_types=1);

namespace App\Observers;

use App\Contracts\Search\MeilisearchGlobalSearchServiceInterface;
use App\Jobs\SyncGlobalSearchChunksJob;
use App\Models\GlobalSearch;
use App\Models\GlobalSearchChunk;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

final class GlobalSearchObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(GlobalSearch $globalSearch): void
    {
        SyncGlobalSearchChunksJob::dispatch($globalSearch->id);
    }

    public function deleting(GlobalSearch $globalSearch): void
    {
        $chunkIds = GlobalSearchChunk::query()
            ->where('global_search_id', $globalSearch->id)
            ->orderBy('chunk_index')
            ->get()
            ->map(fn (GlobalSearchChunk $chunk): string => $chunk->meilisearchId())
            ->all();

        resolve(MeilisearchGlobalSearchServiceInterface::class)->deleteDocuments($chunkIds);
    }
}
