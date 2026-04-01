<?php

namespace App\Service\Search;

use App\Models\SearchItem;

class SearchDocumentProjector
{
    public function upsert(array $payload): SearchItem
    {
        return SearchItem::updateOrCreate(
            ['id' => $payload['id']],
            $payload
        );
    }

    public function remove(string $identifier): void
    {
        SearchItem::where('id', $identifier)->delete();
    }
}
