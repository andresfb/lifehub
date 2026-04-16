<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search\GlobalSearchEmbeddingServiceInterface;
use App\Contracts\Search\GlobalSearchQueryServiceInterface;
use App\Contracts\Search\MeilisearchGlobalSearchServiceInterface;
use App\Models\User;

final readonly class GlobalSearchQueryService implements GlobalSearchQueryServiceInterface
{
    public function __construct(
        private GlobalSearchEmbeddingServiceInterface   $embeddingService,
        private MeilisearchGlobalSearchServiceInterface $meilisearch,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(User $user, string $query, int $limit = 20, array $filters = []): array
    {
        $resolved = $this->embeddingService->resolve($user);
        $embedding = null;

        if ($resolved !== null) {
            $this->meilisearch->ensureIndex($this->embeddingService->dimensions());
            $embedding = $this->embeddingService->embed($user, [$query], $resolved)[0] ?? null;
        }

        $response = $this->meilisearch->search(
            user: $user,
            query: $query,
            limit: $limit,
            filters: $filters,
            embedding: is_array($embedding) ? $embedding : null,
        );

        $hits = $response['hits'] ?? [];
        $hits = is_array($hits) ? $this->normalizeHits($hits) : [];

        return [
            'query' => $query,
            'mode' => $embedding === null ? 'keyword' : 'hybrid',
            'hits' => $this->groupHits($hits),
            'raw_hits_count' => count($hits),
            'processing_time_ms' => $response['processingTimeMs'] ?? null,
            'estimated_total_hits' => $response['estimatedTotalHits'] ?? null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $hits
     * @return array<int, array<string, mixed>>
     */
    private function groupHits(array $hits): array
    {
        return collect($hits)
            ->groupBy('global_search_id')
            ->map(function ($items): array {
                $items = collect($items)->values();
                $top = $items->first();

                if (! is_array($top)) {
                    return [];
                }

                return [
                    'global_search_id' => is_numeric($top['global_search_id'] ?? null) ? (int) $top['global_search_id'] : 0,
                    'entity_type' => $top['entity_type'] ?? null,
                    'entity_id' => is_numeric($top['entity_id'] ?? null) ? (int) $top['entity_id'] : null,
                    'module' => $top['module'] ?? null,
                    'title' => $top['title'] ?? '',
                    'body' => $top['body'] ?? '',
                    'tags' => $top['tags'] ?? [],
                    'keywords' => $top['keywords'] ?? [],
                    'urls' => $top['urls'] ?? [],
                    'is_private' => (bool) ($top['is_private'] ?? false),
                    'is_archived' => (bool) ($top['is_archived'] ?? false),
                    'score' => $top['_rankingScore'] ?? null,
                    'matched_chunks' => $items
                        ->take(3)
                        ->filter(is_array(...))
                        ->map(static fn (array $hit): array => [
                            'global_search_chunk_id' => $hit['global_search_chunk_id'] ?? null,
                            'chunk_index' => $hit['chunk_index'] ?? null,
                            'content' => is_array($hit['_formatted'] ?? null)
                                ? ($hit['_formatted']['content'] ?? $hit['content'] ?? '')
                                : ($hit['content'] ?? ''),
                            'score' => $hit['_rankingScore'] ?? null,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int|string, mixed>  $hits
     * @return array<int, array<string, mixed>>
     */
    private function normalizeHits(array $hits): array
    {
        $normalized = [];

        foreach ($hits as $hit) {
            if (is_array($hit)) {
                $normalized[] = $this->normalizeHit($hit);
            }
        }

        return $normalized;
    }

    /**
     * @param  array<int|string, mixed>  $hit
     * @return array<string, mixed>
     */
    private function normalizeHit(array $hit): array
    {
        return array_filter($hit, static function ($key) {
            return is_string($key);
        }, ARRAY_FILTER_USE_KEY);
    }
}
