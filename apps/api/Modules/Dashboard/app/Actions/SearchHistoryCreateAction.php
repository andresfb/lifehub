<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\SearchHistory;
use Modules\Dashboard\Dtos\SearchHistoryItem;
use Throwable;

final readonly class SearchHistoryCreateAction
{
    /**
     * @throws Throwable
     */
    public function handle(SearchHistoryItem $item): string
    {
        return DB::transaction(static function () use ($item): string {
            $hash = md5($item->getQuery());

            SearchHistory::query()
                ->updateOrCreate(
                    ['hash' => $hash],
                    $item->toArray()
                );

            return $hash;
        });
    }
}
