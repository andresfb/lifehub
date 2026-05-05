<?php

namespace App\Actions;

use App\Models\Tag;
use Illuminate\Support\Collection;

final readonly class SearchTagAction
{
    public function handle(int $userId, string $query): Collection
    {
        return Tag::search($query)
            ->where('user_id', $userId)
            ->get();
    }
}
