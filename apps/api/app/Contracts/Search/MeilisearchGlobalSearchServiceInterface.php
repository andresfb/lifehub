<?php

declare(strict_types=1);

namespace App\Contracts\Search;

use App\Models\User;

interface MeilisearchGlobalSearchServiceInterface
{
    public function ensureIndex(int $dimensions): void;

    /**
     * @param  array<int, array<string, mixed>>  $documents
     */
    public function upsertDocuments(array $documents): void;

    /**
     * @param  array<int, string>  $ids
     */
    public function deleteDocuments(array $ids): void;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<int, float>|null  $embedding
     * @return array<string, mixed>
     */
    public function search(User $user, string $query, int $limit, array $filters = [], ?array $embedding = null): array;
}
