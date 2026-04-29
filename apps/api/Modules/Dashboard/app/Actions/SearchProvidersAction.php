<?php

declare(strict_types=1);

namespace Modules\Dashboard\Actions;

use Modules\Dashboard\Http\Resources\Api\V1\SearchProviderCollection;
use Modules\Dashboard\Models\SearchProvider;

final class SearchProvidersAction
{
    public function handle(int $userId): SearchProviderCollection
    {
        return new SearchProviderCollection(
            SearchProvider::getUserProviders($userId)
        );
    }
}
