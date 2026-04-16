<?php

declare(strict_types=1);

namespace App\Contracts\Search;

use App\Dtos\AI\ResolvedUserAiProvider;
use App\Models\User;

interface GlobalSearchEmbeddingServiceInterface
{
    public function resolve(User $user): ?ResolvedUserAiProvider;

    /**
     * @param  array<int, string>  $inputs
     * @return array<int, array<int, float>>
     */
    public function embed(User $user, array $inputs, ?ResolvedUserAiProvider $resolved = null): array;

    public function dimensions(): int;
}
