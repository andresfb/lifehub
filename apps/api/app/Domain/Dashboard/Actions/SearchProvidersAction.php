<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Actions;

use App\Domain\Dashboard\Dtos\SearchProviderItem;
use App\Domain\Dashboard\Models\SearchProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class SearchProvidersAction
{
    /**
     * @return Collection<SearchProviderItem>
     */
    public function handle(int $userId): Collection
    {
        $cached = Cache::tags("SearchProviders:$userId")
            ->remember(
                md5("SearchProviders:$userId"),
                now()->addMonth(),
                function () use ($userId) {
                    return SearchProvider::query()
                        ->where('user_id', $userId)
                        ->where('active', true)
                        ->orderBy('order')
                        ->get()
                        ->map(function (SearchProvider $provider): array {
                            return $provider->only('id', 'user_id', 'name', 'url', 'order');
                        })->all();
                }
            );

        return collect($cached)->map(function (array $item): SearchProviderItem {
            return SearchProviderItem::from($item);
        });
    }
}
