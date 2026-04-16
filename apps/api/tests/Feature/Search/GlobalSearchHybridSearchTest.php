<?php

declare(strict_types=1);

use App\Contracts\Search\GlobalSearchEmbeddingServiceInterface;
use App\Contracts\Search\GlobalSearchQueryServiceInterface;
use App\Contracts\Search\MeilisearchGlobalSearchServiceInterface;
use App\Contracts\Search\TokenTextChunkerInterface;
use App\Dtos\AI\ResolvedUserAiProvider;
use App\Jobs\SyncGlobalSearchChunksJob;
use App\Models\GlobalSearch;
use App\Models\GlobalSearchChunk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Ai\Enums\Lab;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('it builds chunk search documents from global search payloads', function (): void {
    Queue::fake();

    $globalSearch = GlobalSearch::factory()->create([
        'title' => 'Quarterly planning',
        'body' => 'Roadmap priorities',
        'tags' => ['planning'],
        'keywords' => ['roadmap'],
        'is_private' => false,
        'is_archived' => false,
    ]);
    $chunk = GlobalSearchChunk::factory()->for($globalSearch)->create([
        'chunk_index' => 2,
        'content' => 'Roadmap priorities for the next quarter.',
        'content_hash' => hash('sha256', 'Roadmap priorities for the next quarter.'),
    ]);

    $document = $chunk->toSearchDocument([0.1, 0.2, 0.3]);

    expect($document['id'])->toBe("{$globalSearch->id}_2")
        ->and($document['global_search_id'])->toBe($globalSearch->id)
        ->and($document['title'])->toBe('Quarterly planning')
        ->and($document['content'])->toBe('Roadmap priorities for the next quarter.')
        ->and($document['_vectors']['global_search_user_provided'])->toBe([0.1, 0.2, 0.3]);
});

test('it queues chunk synchronization when global search records are saved', function (): void {
    Queue::fake();

    $globalSearch = GlobalSearch::factory()->create();

    Queue::assertPushed(
        SyncGlobalSearchChunksJob::class,
        fn (SyncGlobalSearchChunksJob $job): bool => $job->globalSearchId === $globalSearch->id,
    );
});

test('it syncs changed chunks and removes stale chunks', function (): void {
    Queue::fake();

    $globalSearch = GlobalSearch::factory()->create([
        'title' => 'Meeting notes',
        'body' => 'Original body',
    ]);
    $staleChunk = GlobalSearchChunk::factory()->for($globalSearch)->create([
        'chunk_index' => 4,
        'content' => 'Old content',
        'content_hash' => hash('sha256', 'Old content'),
    ]);
    $meilisearch = new class implements MeilisearchGlobalSearchServiceInterface
    {
        public array $deleted = [];

        public array $upserted = [];

        public function ensureIndex(int $dimensions): void {}

        public function upsertDocuments(array $documents): void
        {
            $this->upserted = $documents;
        }

        public function deleteDocuments(array $ids): void
        {
            $this->deleted = $ids;
        }

        public function search(User $user, string $query, int $limit, array $filters = [], ?array $embedding = null): array
        {
            return [];
        }
    };
    $chunker = new class implements TokenTextChunkerInterface
    {
        public function chunk(string $text, string $model = 'text-embedding-3-small'): array
        {
            return ['First chunk', 'Second chunk'];
        }
    };
    $embeddingService = new class implements GlobalSearchEmbeddingServiceInterface
    {
        public function resolve(User $user): ?ResolvedUserAiProvider
        {
            return null;
        }

        public function embed(User $user, array $inputs, ?ResolvedUserAiProvider $resolved = null): array
        {
            return [];
        }

        public function dimensions(): int
        {
            return 1536;
        }
    };

    (new SyncGlobalSearchChunksJob($globalSearch->id))->handle($chunker, $embeddingService, $meilisearch);

    expect(GlobalSearchChunk::query()->where('global_search_id', $globalSearch->id)->count())->toBe(2)
        ->and($meilisearch->deleted)->toBe([$staleChunk->meilisearchId()])
        ->and($meilisearch->upserted)->toHaveCount(2)
        ->and($meilisearch->upserted[0])->not->toHaveKey('_vectors');
});

test('it adds embeddings when the user has an embedding capable provider', function (): void {
    Queue::fake();

    $globalSearch = GlobalSearch::factory()->create();
    $meilisearch = new class implements MeilisearchGlobalSearchServiceInterface
    {
        public array $upserted = [];

        public function ensureIndex(int $dimensions): void {}

        public function upsertDocuments(array $documents): void
        {
            $this->upserted = $documents;
        }

        public function deleteDocuments(array $ids): void {}

        public function search(User $user, string $query, int $limit, array $filters = [], ?array $embedding = null): array
        {
            return [];
        }
    };
    $chunker = new class implements TokenTextChunkerInterface
    {
        public function chunk(string $text, string $model = 'text-embedding-3-small'): array
        {
            return ['Embeddable chunk'];
        }
    };
    $embeddingService = new class implements GlobalSearchEmbeddingServiceInterface
    {
        public function resolve(User $user): ?ResolvedUserAiProvider
        {
            return new ResolvedUserAiProvider(
                providerName: 'user-provider',
                providerCode: 'openai',
                lab: Lab::OpenAI,
                model: 'text-embedding-3-small',
                featureCapabilities: ['supports_embeddings' => true],
            );
        }

        public function embed(User $user, array $inputs, ?ResolvedUserAiProvider $resolved = null): array
        {
            return [[0.1, 0.2, 0.3]];
        }

        public function dimensions(): int
        {
            return 1536;
        }
    };

    (new SyncGlobalSearchChunksJob($globalSearch->id))->handle($chunker, $embeddingService, $meilisearch);

    $chunk = GlobalSearchChunk::query()->where('global_search_id', $globalSearch->id)->firstOrFail();

    expect($meilisearch->upserted[0]['_vectors']['global_search_user_provided'])->toBe([0.1, 0.2, 0.3])
        ->and($chunk->embedded_provider_code)->toBe('openai')
        ->and($chunk->embedded_model)->toBe('text-embedding-3-small')
        ->and($chunk->embedded_content_hash)->toBe($chunk->content_hash);
});

test('it exposes an authenticated global search endpoint', function (): void {
    $user = User::factory()->create();

    app()->instance(GlobalSearchQueryServiceInterface::class, new class implements GlobalSearchQueryServiceInterface
    {
        public function search(User $user, string $query, int $limit = 20, array $filters = []): array
        {
            return [
                'query' => $query,
                'mode' => 'keyword',
                'hits' => [
                    [
                        'global_search_id' => 1,
                        'title' => 'Result',
                        'matched_chunks' => [],
                    ],
                ],
            ];
        }
    });

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/search?q=planning&limit=5')
        ->assertOk()
        ->assertJsonPath('data.query', 'planning')
        ->assertJsonPath('data.mode', 'keyword')
        ->assertJsonPath('data.hits.0.title', 'Result');
});
