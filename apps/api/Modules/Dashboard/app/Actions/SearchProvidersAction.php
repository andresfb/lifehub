<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Illuminate\Support\Collection;
use Modules\Dashboard\Models\SearchProvider;

final class SearchProvidersAction
{
    /**
     * @return Collection<int, SearchProvider>
     */
    public function handle(int $userId): Collection
    {
        return SearchProvider::getUserProviders($userId);
    }
}
