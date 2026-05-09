<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Tag;
use Illuminate\Support\Collection;

final readonly class SearchTagAction
{
    /**
     * @return Collection<int, Tag>
     */
    public function handle(int $userId, string $query): Collection
    {
        return Tag::search($query)
            ->where('user_id', $userId)
            ->get();
    }
}
