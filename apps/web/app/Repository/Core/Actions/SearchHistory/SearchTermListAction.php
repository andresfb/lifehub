<?php

declare(strict_types=1);

namespace App\Repository\Core\Actions\SearchHistory;

use App\Repository\Core\Dtos\SearchHistory\SearchTermItem;
use App\Repository\Core\Services\SearchHistory\ApiSearchHistoryService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final readonly class SearchTermListAction
{
    public function __construct(
        private ApiSearchHistoryService $searchHistoryService
    ) {}

    /**
     * @return Collection<string, SearchTermItem>
     *
     * @throws Exception
     */
    public function handle(int $userId, SearchTermItem $item): Collection
    {
        $cacheKey = md5(sprintf(
            'search:suggestions:%s:%s:%s',
            $userId,
            $item->module,
            $item->type,
        ));

        $cached = Cache::tags(["search:history:{$userId}"])
            ->remember($cacheKey, now()->addMinutes(10), function () use ($userId, $item): array {
                return $this->searchHistoryService->listTerms($userId, $item);
            });

        if (blank($cached)) {
            return collect();
        }

        return collect($cached)
            ->map(fn (array $item): SearchTermItem => SearchTermItem::from($item));
    }
}
