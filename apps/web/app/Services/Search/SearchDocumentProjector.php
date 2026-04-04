<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Models\GlobalSearch;

final class SearchDocumentProjector
{
    public function upsert(array $payload, int $userId): GlobalSearch
    {
        return GlobalSearch::query()
            ->updateOrCreate(
                [
                    'creator_id' => $payload['creator_id'],
                    'user_id' => $userId,
                ],
                $payload
            );
    }

    public function remove(string $identifier): void
    {
        GlobalSearch::query()
            ->where('creator_id', $identifier)
            ->delete();
    }
}
