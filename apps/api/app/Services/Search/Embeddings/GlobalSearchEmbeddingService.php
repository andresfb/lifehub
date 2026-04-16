<?php

declare(strict_types=1);

namespace App\Services\Search\Embeddings;

use App\Contracts\Search\GlobalSearchEmbeddingServiceInterface;
use App\Dtos\AI\ResolvedUserAiProvider;
use App\Enums\AiModelFeatures;
use App\Exceptions\UserAiConfigurationException;
use App\Models\User;
use App\Services\AI\UserAiResolver;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Exceptions\FailoverableException;

final readonly class GlobalSearchEmbeddingService implements GlobalSearchEmbeddingServiceInterface
{
    public function __construct(private UserAiResolver $resolver) {}

    public function resolve(User $user): ?ResolvedUserAiProvider
    {
        try {
            return $this->resolver->resolve($user, AiModelFeatures::embeddings);
        } catch (UserAiConfigurationException) {
            return null;
        }
    }

    /**
     * @return array<int, array<int, float>>
     * @throws FailoverableException
     */
    public function embed(User $user, array $inputs, ?ResolvedUserAiProvider $resolved = null): array
    {
        if ($inputs === []) {
            return [];
        }

        $resolved ??= $this->resolve($user);

        if ($resolved === null) {
            return [];
        }

        $embeddings = Embeddings::for($inputs)
            ->dimensions($this->dimensions())
            ->timeout($this->integerConfig('search.hybrid.timeout', 30))
            ->generate($resolved->providerName, $resolved->model)
            ->embeddings;

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
        return $this->integerConfig('search.hybrid.dimensions', 1536);
    }

    private function integerConfig(string $key, int $default): int
    {
        $value = config($key, $default);

        return is_numeric($value) ? (int) $value : $default;
    }
}
