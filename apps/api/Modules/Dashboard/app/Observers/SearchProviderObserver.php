<?php

declare(strict_types=1);

namespace Modules\Dashboard\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Dashboard\Models\SearchProvider;

final class SearchProviderObserver
{
    public function creating(SearchProvider $provider): void
    {
        if (filled($provider->order)) {
            return;
        }

        $provider->order = SearchProvider::query()->max('order') + 1;
    }

    public function saved(SearchProvider $provider): void
    {
        if ($provider->default) {
            SearchProvider::query()
                ->where('user_id', $provider->user_id)
                ->where('id', '!=', $provider->id)
                ->where('default', true)
                ->toBase()
                ->update(['default' => false]);
        }

        Cache::tags("SearchProviders:{$provider->user_id}")->flush();
    }
}
