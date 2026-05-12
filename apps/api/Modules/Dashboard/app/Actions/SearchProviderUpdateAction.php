<?php

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Dashboard\Dtos\SearchProviderItem;
use Modules\Dashboard\Models\SearchProvider;
use Throwable;

final readonly class SearchProviderUpdateAction
{
    /**
     * @throws Throwable
     */
    public function handle(SearchProvider $provider, SearchProviderItem $item): void
    {
        DB::transaction(static function () use ($provider, $item): void {
            $provider->update(
                array_merge(
                    ['url' => $item->getUrl()],
                    $item->except('url')->toArray(),
                )
            );
        });
    }
}
