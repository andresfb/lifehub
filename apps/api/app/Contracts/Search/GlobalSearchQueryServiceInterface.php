<?php

declare(strict_types=1);

namespace App\Contracts\Search;

use App\Models\User;

interface GlobalSearchQueryServiceInterface
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(User $user, string $query, int $limit = 20, array $filters = []): array;
}
