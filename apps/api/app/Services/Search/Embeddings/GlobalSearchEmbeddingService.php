<?php

declare(strict_types=1);

namespace App\Services\Search\Embeddings;

use App\Contracts\Search\GlobalSearchEmbeddingServiceInterface;
use App\Exceptions\UserAiConfigurationException;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Exceptions\FailoverableException;
use Modules\Core\Dtos\AI\ResolvedUserAiProvider;
use Modules\Core\Enums\AiModelFeatures;
use Modules\Core\Services\AI\UserAiResolver;

final readonly class GlobalSearchEmbeddingService implements GlobalSearchEmbeddingServiceInterface
{
    public function __construct(private UserAiResolver $resolver) {}

    public function resolve(User $user): ?ResolvedUserAiProvider
    {
        try {
            return $this->resolver->resolve(
                user: $user,
                feature: AiModelFeatures::embeddings,
                random: false,
            );
        } catch (UserAiConfigurationException) {
            return null;
        }
    }

    /**
     * @return array<int, array<int, float>>
     * @throws FailoverableException
     */
    public function embed(User $user, array $inputs, ?ResolvedUserAiProvider $resolved = null, bool $cache = false): array
    {
        if ($inputs === []) {
            return [];
        }

        $resolved ??= $this->resolve($user);

        if ($resolved === null) {
            return [];
        }

        $embedder = Embeddings::for($inputs)
            ->dimensions($this->dimensions())
            ->timeout(Config::integer('search.hybrid.timeout', 30));

        if ($cache === true) {
            $embedder = $embedder->cache();
        }

        $embeddings = $embedder->generate(
            $resolved->code,
            $resolved->model
        )->embeddings;

        $normalized = [];

        foreach ($embeddings as $embedding) {
            if (! is_array($embedding)) {
                continue;
            }

            $values = [];

            foreach ($embedding as $value) {
                $values[] = is_numeric($value) ? (float) $value : 0.0;
            }

            $normalized[] = $values;
        }

        return $normalized;
    }

    public function dimensions(): int
    {
        return Config::integer('search.hybrid.dimensions', 1536);
    }
}
