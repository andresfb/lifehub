<?php

namespace App\Services\Search;

use App\Contracts\Search\GlobalSearchEmbeddingServiceInterface;
use App\Contracts\Search\MeilisearchGlobalSearchServiceInterface;
use App\Contracts\Search\TokenTextChunkerInterface;
use App\Models\GlobalSearch;
use App\Models\GlobalSearchChunk;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use JsonException;
use Modules\Core\Dtos\AI\ResolvedUserAiProvider;
use Throwable;

final readonly class SyncGlobalSearchChunksService
{
    public function __construct(
        private TokenTextChunkerInterface $chunker,
        private GlobalSearchEmbeddingServiceInterface $embeddingService,
        private MeilisearchGlobalSearchServiceInterface $meilisearch,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(int $globalSearchId): void
    {
        $globalSearch = GlobalSearch::query()
            ->with(['user', 'chunks'])
            ->find($globalSearchId);

        if (! $globalSearch instanceof GlobalSearch) {
            return;
        }

        $resolved = $this->embeddingService->resolve($globalSearch->user);
        $dimensions = $this->embeddingService->dimensions();
        $this->meilisearch->ensureIndex($dimensions);

        $content = $this->searchableContent($globalSearch);
        $chunks = $this->chunker->chunk(
            $content,
            $resolved instanceof ResolvedUserAiProvider
                ? $resolved->model
                : 'text-embedding-3-small'
        );

        if ($chunks === []) {
            $this->deleteAllChunks($globalSearch);

            return;
        }

        /** @var EloquentCollection<int, GlobalSearchChunk> $existingChunks */
        $existingChunks = $globalSearch->chunks;
        $existingByIndex = $existingChunks->keyBy('chunk_index');
        /** @var array<int, array<string, mixed>> $documents */
        $documents = [];
        /** @var array<int, int> $seenIndexes */
        $seenIndexes = [];

        DB::transaction(function () use (
            $chunks,
            $existingByIndex,
            $globalSearch,
            $resolved,
            $dimensions,
            &$documents,
            &$seenIndexes
        ): void {
            foreach ($chunks as $index => $content) {
                $seenIndexes[] = $index;
                $hash = hash('sha256', $content);

                /** @var GlobalSearchChunk|null $chunk */
                $chunk = $existingByIndex->get($index);

                if (! $chunk instanceof GlobalSearchChunk || $chunk->content_hash !== $hash) {
                    $chunk = GlobalSearchChunk::query()
                        ->updateOrCreate([
                            'global_search_id' => $globalSearch->id,
                            'chunk_index' => $index,
                        ], [
                            'user_id' => $globalSearch->user_id,
                            'content' => $content,
                            'content_hash' => $hash,
                            'content_length' => mb_strlen($content),
                            'embedded_content_hash' => null,
                            'embedded_at' => null,
                            'embedding_failed_reason' => null,
                            'embedding_failed_at' => null,
                        ],
                    );
                }

                $chunk->setRelation('globalSearch', $globalSearch);
                $embedding = $this->embeddingForChunk($chunk, $globalSearch, $resolved, $dimensions);

                $documents[] = $chunk->toSearchDocument($embedding);
            }
        });

        $staleChunks = GlobalSearchChunk::query()
            ->where('global_search_id', $globalSearch->id)
            ->whereNotIn('chunk_index', $seenIndexes)
            ->get();

        if ($staleChunks->isNotEmpty()) {
            $this->meilisearch->deleteDocuments(
                $staleChunks->map(fn (GlobalSearchChunk $chunk): string => $chunk->meilisearchId())->all()
            );

            GlobalSearchChunk::query()
                ->whereIn('id', $staleChunks->pluck('id'))
                ->delete();
        }

        $this->meilisearch->upsertDocuments($documents);
    }


    /**
     * @return array<int, float>|null
     * @throws Throwable
     */
    private function embeddingForChunk(
        GlobalSearchChunk $chunk,
        GlobalSearch $globalSearch,
        ?ResolvedUserAiProvider $resolved,
        int $dimensions,
    ): ?array {
        if ($resolved === null) {
            return null;
        }

        if (
            $chunk->embedded_provider_code === $resolved->code
            && $chunk->embedded_model === $resolved->model
            && $chunk->embedded_dimensions === $dimensions
            && $chunk->embedded_content_hash === $chunk->content_hash
        ) {
            return null;
        }

        try {
            $embedding = $this->embeddingService->embed($globalSearch->user, [$chunk->content], $resolved)[0] ?? null;

            if (! is_array($embedding) || $embedding === []) {
                return null;
            }

            $chunk->forceFill([
                'embedded_provider_code' => $resolved->code,
                'embedded_model' => $resolved->model,
                'embedded_dimensions' => $dimensions,
                'embedded_content_hash' => $chunk->content_hash,
                'embedded_at' => now(),
                'embedding_failed_reason' => null,
                'embedding_failed_at' => null,
            ])->save();

            return array_map(static fn (mixed $value): float => (float) $value, $embedding);
        } catch (Throwable $throwable) {
            $chunk->forceFill([
                'embedding_failed_reason' => mb_substr($throwable->getMessage(), 0, 1000),
                'embedding_failed_at' => now(),
            ])->save();

            throw $throwable;
        }
    }

    /**
     * @throws JsonException
     */
    private function searchableContent(GlobalSearch $globalSearch): string
    {
        return collect([
            $globalSearch->title,
            $globalSearch->body,
            $this->jsonText($globalSearch->tags),
            $this->jsonText($globalSearch->keywords),
            $this->jsonText($globalSearch->metadata),
            $this->jsonText($globalSearch->urls),
        ])->filter(fn (?string $value): bool => filled($value))
            ->implode("\n\n");
    }

    /**
     * @throws JsonException
     */
    private function jsonText(mixed $value): ?string
    {
        if (! is_array($value) || $value === []) {
            return null;
        }

        return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function deleteAllChunks(GlobalSearch $globalSearch): void
    {
        $chunks = GlobalSearchChunk::query()
            ->where('global_search_id', $globalSearch->id)
            ->get();

        $this->meilisearch->deleteDocuments(
            $chunks->map(fn (GlobalSearchChunk $chunk): string => $chunk->meilisearchId())->all()
        );

        GlobalSearchChunk::query()
            ->where('global_search_id', $globalSearch->id)
            ->delete();
    }
}
