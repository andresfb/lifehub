<?php

declare(strict_types=1);

namespace Modules\Dashboard\Jobs;

use Modules\Dashboard\Models\HomepageItem;
use App\Services\Search\SearchDocumentProjector;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class HomepageItemSavedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $itemId,
    ) {
        $this->delay = now()->addSeconds(10);
    }

    public function handle(SearchDocumentProjector $projector): void
    {
        try {
            $item = HomepageItem::query()
                ->where('id', $this->itemId)
                ->firstOrFail();

            $projector->upsert(
                $item->buildGlobalSearch(),
                $item->user_id,
            );

            Cache::tags("homepage:{$item->user_id}")->flush();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
