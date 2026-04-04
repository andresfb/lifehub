<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Jobs;

use App\Domain\Bookmarks\Models\Marker;
use App\Services\Search\SearchDocumentProjector;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class SearchDocumentUpdatedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $markerId,
    ) {}

    public function handle(SearchDocumentProjector $projector): void
    {
        try {
            $marker = Marker::query()
                ->where('id', $this->markerId)
                ->firstOrFail();

            $projector->upsert(
                $marker->buildGlobalSearch(),
                $marker->user_id,
            );

            $marker->searchable();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
