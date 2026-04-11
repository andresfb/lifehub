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
                    ->orderBy('default')
                    ->orderBy('order')
                    ->get()
                    ->map(fn (SearchProvider $provider): array => $provider->except('active', 'created_at', 'updated_at'))->all()
            );

        return collect($cached)->map(fn (array $item): SearchProviderItem => SearchProviderItem::from($item));
    }
}
