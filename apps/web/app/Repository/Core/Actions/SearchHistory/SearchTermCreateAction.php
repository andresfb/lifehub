<?php

declare(strict_types=1);

namespace App\Repository\Core\Actions\SearchHistory;

use App\Repository\Core\Dtos\SearchHistory\SearchTermItem;
use App\Repository\Core\Services\SearchHistory\ApiSearchHistoryService;
use Exception;
use Illuminate\Support\Facades\Cache;

final readonly class SearchTermCreateAction
{
    public function __construct(
        private ApiSearchHistoryService $searchHistoryService
    ) {}

    /**
     * @throws Exception
     */
    public function handle(int $userId, SearchTermItem $item): void
    {
        if ($this->termCached($userId, $item)) {
            return;
        }

        $this->searchHistoryService->saveTerm($userId, $item);

        Cache::tags(["search:history:{$userId}"])->flush();
    }

    private function termCached(int $userId, SearchTermItem $item): bool
    {
        $cacheKey = md5(sprintf(
            'search:suggestions:%s:%s:%s',
            $userId,
            $item->module,
            $item->type,
        ));

        $list = Cache::tags(["search:history:{$userId}"])
            ->get($cacheKey, collect());

        if ($list->isEmpty()) {
            return false;
        }

        return $list->firstWhere('term', $item->term) !== null;
    }
}
