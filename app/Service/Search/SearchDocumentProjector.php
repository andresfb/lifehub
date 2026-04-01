<?php

namespace App\Service\Search;

use App\Models\SearchItem;

class SearchDocumentProjector
{
    public function upsert(array $payload): SearchItem
    {
        return SearchItem::query()
            ->updateOrCreate(
                ['id' => $payload['id']],
                $payload
            );
    }

    public function remove(string $identifier): void
    {
        SearchItem::query()
            ->where('id', $identifier)
            ->delete();
    }
}
