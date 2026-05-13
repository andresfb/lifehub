<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Dashboard\Dtos\SearchProviderItem;
use Modules\Dashboard\Models\SearchProvider;
use RuntimeException;
use Throwable;

final readonly class SearchProviderCreateAction
{
    /**
     * @throws Throwable
     */
    public function handle(int $userId, SearchProviderItem $item): void
    {
        DB::transaction(static function () use ($userId, $item): void {
            if (SearchProvider::found($userId, $item->name, $item->url)) {
                throw new RuntimeException('Search Provider already exists');
            }

            SearchProvider::query()
                ->create(
                    array_merge(
                        ['user_id' => $userId, 'url' => $item->getUrl()],
                        $item->except('url')->toArray(),
                    )
                );
        });
    }
}
