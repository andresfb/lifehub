<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search\MeilisearchGlobalSearchServiceInterface;
use App\Models\User;
use Exception;
use Meilisearch\Client;
use Meilisearch\Endpoints\Indexes;
use Meilisearch\Exceptions\ApiException;

final class MeilisearchGlobalSearchService implements MeilisearchGlobalSearchServiceInterface
{
    public function client(): Client
    {
        $host = config('scout.meilisearch.host');
        $key = config('scout.meilisearch.key');

        return new Client(
            is_string($host) ? $host : 'http://localhost:7700',
            is_string($key) ? $key : null,
        );
    }

    public function indexUid(): string
    {
        return sprintf(
            "%s%s",
            $this->stringConfig('scout.prefix', ''),
            $this->stringConfig('search.hybrid.index', 'global_search_chunks')
        );
    }

    public function index(): Indexes
    {
        return $this->client()->index($this->indexUid());
    }

    public function ensureIndex(int $dimensions): void
    {
        $uid = $this->indexUid();
        $client = $this->client();

        try {
            $client->getRawIndex($uid);
        } catch (ApiException) {
            $client->createIndex($uid, ['primaryKey' => 'id']);
        }

        $index = $this->index();
        $index->updateFilterableAttributes([
            'user_id',
            'global_search_id',
            'entity_type',
            'entity_id',
            'module',
            'is_private',
            'is_archived',
        ]);
        $index->updateSortableAttributes([
            'source_updated_at',
            'updated_at',
            'chunk_index',
        ]);
        $index->updateSearchableAttributes([
            'title',
            'body',
            'content',
            'tags',
            'keywords',
        ]);
        $index->updateEmbedders([
            $this->embedder() => [
                'source' => 'userProvided',
                'dimensions' => $dimensions,
            ],
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $documents
     */
    public function upsertDocuments(array $documents): void
    {
        if ($documents === []) {
            return;
        }

        $this->index()->addDocuments($documents, 'id');
    }

    /**
     * @param array<int, string> $ids
     * @throws Exception
     */
    public function deleteDocuments(array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $this->index()->deleteDocuments($ids);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<int, float>|null  $embedding
     * @return array<string, mixed>
     */
    public function search(User $user, string $query, int $limit, array $filters = [], ?array $embedding = null): array
    {
        $searchParams = [
            'limit' => max(1, min($limit, 100)),
            'filter' => $this->buildFilter($user, $filters),
            'showRankingScore' => true,
            'attributesToCrop' => ['content'],
            'cropLength' => 80,
        ];

        if ($embedding !== null) {
            $searchParams['vector'] = $embedding;
            $searchParams['hybrid'] = [
                'semanticRatio' => $this->floatConfig('search.hybrid.semantic_ratio', 0.7),
                'embedder' => $this->embedder(),
            ];
        }

        $response = $this->index()->search($query, $searchParams, ['raw' => true]);

        if (! is_array($response)) {
            return [];
        }

        /** @var array<string, mixed> $response */
        return $response;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, string>
     */
    private function buildFilter(User $user, array $filters): array
    {
        $conditions = ['user_id = '.$user->id];

        foreach (['module', 'entity_type'] as $field) {
            if (filled($filters[$field] ?? null)) {
                $value = $filters[$field];
                $conditions[] = sprintf('%s = "%s"', $field, addslashes(is_scalar($value) ? (string) $value : ''));
            }
        }

        foreach (['is_private', 'is_archived'] as $field) {
            if (array_key_exists($field, $filters)) {
                $conditions[] = sprintf('%s = %s', $field, $filters[$field] ? 'true' : 'false');
            }
        }

        return $conditions;
    }

    private function embedder(): string
    {
        return $this->stringConfig('search.hybrid.embedder', 'global_search_user_provided');
    }

    private function stringConfig(string $key, string $default): string
    {
        $value = config($key, $default);

        return is_string($value) && $value !== '' ? $value : $default;
    }

    private function floatConfig(string $key, float $default): float
    {
        $value = config($key, $default);

        return is_numeric($value) ? (float) $value : $default;
    }
}
