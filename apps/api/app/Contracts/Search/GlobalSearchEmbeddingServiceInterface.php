<?php

declare(strict_types=1);

namespace App\Contracts\Search;

use App\Models\User;
use Modules\Core\Dtos\AI\ResolvedUserAiProvider;

interface GlobalSearchEmbeddingServiceInterface
{
    public function resolve(User $user): ?ResolvedUserAiProvider;

    /**
     * @param  array<int, string>  $inputs
     * @return array<int, array<int, float>>
     */
    public function embed(User $user, array $inputs, ?ResolvedUserAiProvider $resolved = null, bool $cache = false): array;

    public function dimensions(): int;
}
