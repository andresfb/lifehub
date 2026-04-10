<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Dashboard\Dtos\SearchProviderItem;
use Modules\Dashboard\Models\SearchProvider;

final class SearchProvidersAction
{
    /**
     * @return Collection<SearchProviderItem>
     */
    public function handle(int $userId): Collection
    {
        $cached = Cache::tags("SearchProviders:{$userId}")
            ->remember(
                md5("SearchProviders:{$userId}"),
                now()->addMonth(),
                fn () => SearchProvider::query()
                    ->where('user_id', $userId)
                    ->where('active', true)
                    ->orderBy('order')
                    ->get()
                    ->map(fn (SearchProvider $provider): array => $provider->only('id', 'user_id', 'name', 'url', 'order'))->all()
            );

        return collect($cached)->map(fn (array $item): SearchProviderItem => SearchProviderItem::from($item));
    }
}
