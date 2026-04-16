Absolutely — here’s a complete **chunk-based Laravel pipeline** for Meilisearch hybrid search with **user-provided embeddings**.

This version gives you:

* `documents` as the source of truth
* `document_chunks` to track chunk content + hashes
* embeddings generated in Laravel with each user’s own API key
* Meilisearch indexing at the **chunk level**
* search results grouped back to the parent document
* re-embedding only when a chunk changed ✔️

I’ll keep OpenAI as the embedding provider example, but the structure works the same for others.

---

# Architecture

## Database

* `documents`
* `document_chunks`

## Flow

1. User uploads or updates a document
2. A job splits it into chunks
3. Each chunk gets hashed
4. Only new/changed chunks are re-embedded
5. Chunks are indexed into Meilisearch with `_vectors`
6. Search runs against the chunk index
7. Results are grouped by `document_id`

---

# 1) Migrations

## database/migrations/2026_04_16_000001_add_ai_search_columns_to_users_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ai_provider')->nullable()->after('remember_token');
            $table->text('ai_api_key')->nullable()->after('ai_provider');
            $table->boolean('ai_search_enabled')->default(false)->after('ai_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ai_provider',
                'ai_api_key',
                'ai_search_enabled',
            ]);
        });
    }
};
```

## `database/migrations/xxxx_xx_xx_create_documents_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('body')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
```

## `database/migrations/xxxx_xx_xx_create_document_chunks_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('chunk_index');
            $table->longText('content');
            $table->string('content_hash', 64);
            $table->unsignedInteger('content_length')->default(0);
            $table->timestamps();

            $table->unique(['document_id', 'chunk_index']);
            $table->index(['user_id', 'document_id']);
            $table->index('content_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
```

---

# 2) Models

## User modifications

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'ai_provider',
        'ai_api_key',
        'ai_search_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'ai_api_key',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ai_search_enabled' => 'boolean',
    ];

    protected function aiApiKey(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?string {
                if ($value === null) {
                    return null;
                }

                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException) {
                    return null;
                }
            },
            set: fn (?string $value): ?string => $value ? Crypt::encryptString($value) : null
        );
    }

    public function hasAiSearchEnabled(): bool
    {
        return $this->ai_search_enabled
            && $this->ai_provider === 'openai'
            && filled($this->ai_api_key);
    }
}
```

## `app/Models/Document.php`

```php
<?php

namespace App\Models;

use App\Jobs\SyncDocumentChunksJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function (Document $document): void {
            SyncDocumentChunksJob::dispatch($document->id);
        });

        static::deleted(function (Document $document): void {
            // child chunks are cascade-deleted; their deletion job is handled in SyncDocumentChunksJob
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class);
    }

    public function searchableContent(): string
    {
        $parts = array_filter([
            $this->title,
            $this->body,
            is_array($this->metadata) ? json_encode($this->metadata, JSON_UNESCAPED_UNICODE) : null,
        ]);

        return implode("\n\n", $parts);
    }
}
```

## `app/Models/DocumentChunk.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentChunk extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        'chunk_index',
        'content',
        'content_hash',
        'content_length',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meilisearchId(): string
    {
        return "{$this->document_id}_{$this->chunk_index}";
    }

    public function toSearchDocument(?array $embedding = null): array
    {
        $payload = [
            'id' => $this->meilisearchId(),
            'document_chunk_id' => $this->id,
            'document_id' => $this->document_id,
            'user_id' => $this->user_id,
            'chunk_index' => $this->chunk_index,
            'content' => $this->content,
            'content_hash' => $this->content_hash,
            'created_at' => optional($this->created_at)?->toAtomString(),
            'updated_at' => optional($this->updated_at)?->toAtomString(),
        ];

        if ($this->relationLoaded('document') && $this->document) {
            $payload['document_title'] = $this->document->title;
            $payload['document_metadata'] = $this->document->metadata ?? [];
        }

        if ($embedding !== null) {
            $payload['_vectors'] = [
                config('services.meilisearch.embedder_name', 'default') => $embedding,
            ];
        }

        return $payload;
    }
}
```

---

# 3) Chunker service

This version is smarter than raw fixed-size slicing. It tries to split by paragraphs first, then sentences, then hard-cuts if needed.

## `app/Services/Embeddings/TextChunker.php`

```php
<?php

namespace App\Services\Embeddings;

class TextChunker
{
    /**
     * @return array<int, string>
     */
    public function chunk(
        string $text,
        int $targetChars = 3000,
        int $overlapChars = 300
    ): array {
        $text = $this->normalize($text);

        if ($text === '') {
            return [];
        }

        $paragraphs = preg_split("/\n{2,}/u", $text) ?: [];
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if (mb_strlen($paragraph) > $targetChars) {
                $sentences = $this->splitIntoSentences($paragraph);

                foreach ($sentences as $sentence) {
                    $sentence = trim($sentence);

                    if ($sentence === '') {
                        continue;
                    }

                    if ($current === '') {
                        $current = $sentence;
                        continue;
                    }

                    $candidate = $current . ' ' . $sentence;

                    if (mb_strlen($candidate) <= $targetChars) {
                        $current = $candidate;
                        continue;
                    }

                    $chunks[] = $current;
                    $current = $this->withOverlap($current, $sentence, $overlapChars, $targetChars);
                }

                continue;
            }

            if ($current === '') {
                $current = $paragraph;
                continue;
            }

            $candidate = $current . "\n\n" . $paragraph;

            if (mb_strlen($candidate) <= $targetChars) {
                $current = $candidate;
                continue;
            }

            $chunks[] = $current;
            $current = $this->withOverlap($current, $paragraph, $overlapChars, $targetChars);
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $this->hardLimitOversizedChunks($chunks, $targetChars, $overlapChars);
    }

    protected function normalize(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/[ \t]+/u", ' ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/u", "\n\n", $text) ?? $text;

        return trim($text);
    }

    /**
     * @return array<int, string>
     */
    protected function splitIntoSentences(string $text): array
    {
        $parts = preg_split('/(?<=[.!?])\s+/u', $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }

    protected function withOverlap(
        string $previousChunk,
        string $nextSegment,
        int $overlapChars,
        int $targetChars
    ): string {
        $tail = mb_substr($previousChunk, max(0, mb_strlen($previousChunk) - $overlapChars));

        $candidate = trim($tail . ' ' . $nextSegment);

        if (mb_strlen($candidate) <= $targetChars) {
            return $candidate;
        }

        return mb_substr($candidate, 0, $targetChars);
    }

    /**
     * @param array<int, string> $chunks
     * @return array<int, string>
     */
    protected function hardLimitOversizedChunks(array $chunks, int $targetChars, int $overlapChars): array
    {
        $final = [];

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);

            if ($chunk === '') {
                continue;
            }

            if (mb_strlen($chunk) <= $targetChars) {
                $final[] = $chunk;
                continue;
            }

            $start = 0;
            $length = mb_strlen($chunk);

            while ($start < $length) {
                $piece = trim(mb_substr($chunk, $start, $targetChars));

                if ($piece !== '') {
                    $final[] = $piece;
                }

                $step = max(1, $targetChars - $overlapChars);
                $start += $step;
            }
        }

        return $final;
    }
}
```

---

# 4) Embedding service

## `app/Services/Embeddings/EmbeddingService.php`

```php
<?php

namespace App\Services\Embeddings;

interface EmbeddingService
{
    /**
     * @return array<int, float>
     */
    public function embed(string $text, string $apiKey): array;
}
```

## `app/Services/Embeddings/OpenAiEmbeddingService.php`

```php
<?php

namespace App\Services\Embeddings;

use OpenAI;

class OpenAiEmbeddingService implements EmbeddingService
{
    public function embed(string $text, string $apiKey): array
    {
        $client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri(config('services.openai.base_url'))
            ->make();

        $response = $client->embeddings()->create([
            'model' => config('services.openai.embedding_model', 'text-embedding-3-small'),
            'input' => $this->normalizeForEmbedding($text),
        ]);

        $embedding = $response->embeddings[0]->embedding ?? null;

        if (! is_array($embedding) || $embedding === []) {
            throw new \RuntimeException('Embedding provider returned an empty embedding.');
        }

        return array_map(static fn ($value): float => (float) $value, $embedding);
    }

    protected function normalizeForEmbedding(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        if ($text === '') {
            $text = '[empty]';
        }

        return $text;
    }
}
```

---

# 5) Meilisearch service

This version works on a **chunk index**.

## `app/Services/Search/MeilisearchService.php`

```php
<?php

namespace App\Services\Search;

use Meilisearch\Client;
use Meilisearch\Contracts\IndexesQuery;

class MeilisearchService
{
    public function client(): Client
    {
        return new Client(
            config('services.meilisearch.host'),
            config('services.meilisearch.key')
        );
    }

    public function documentChunksIndexUid(): string
    {
        return config('services.meilisearch.index_prefix', '')
            . config('services.meilisearch.document_chunks_index', 'document_chunks');
    }

    public function documentChunksIndex(): IndexesQuery
    {
        return $this->client()->index($this->documentChunksIndexUid());
    }

    public function ensureDocumentChunksIndex(): void
    {
        $uid = $this->documentChunksIndexUid();
        $client = $this->client();

        $existing = collect($client->getRawIndexes()['results'] ?? [])
            ->firstWhere('uid', $uid);

        if (! $existing) {
            $client->createIndex($uid, ['primaryKey' => 'id']);
        }

        $index = $client->index($uid);

        $index->updateFilterableAttributes([
            'user_id',
            'document_id',
        ]);

        $index->updateSortableAttributes([
            'chunk_index',
            'created_at',
            'updated_at',
        ]);

        $index->updateSearchableAttributes([
            'document_title',
            'content',
        ]);

        $index->updateDisplayedAttributes([
            'id',
            'document_chunk_id',
            'document_id',
            'user_id',
            'chunk_index',
            'document_title',
            'document_metadata',
            'content',
            'content_hash',
            'created_at',
            'updated_at',
        ]);
    }

    public function upsertDocuments(array $documents): void
    {
        if ($documents === []) {
            return;
        }

        $this->documentChunksIndex()->addDocuments($documents, 'id');
    }

    public function deleteDocuments(array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $this->documentChunksIndex()->deleteDocuments($ids);
    }

    public function deleteDocumentById(string $id): void
    {
        $this->documentChunksIndex()->deleteDocument($id);
    }
}
```

---

# 6) Config

## `config/services.php`

```php
<?php

return [

    'openai' => [
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
    ],

    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index_prefix' => env('MEILISEARCH_INDEX_PREFIX', ''),
        'document_chunks_index' => env('MEILISEARCH_DOCUMENT_CHUNKS_INDEX', 'document_chunks'),
        'semantic_ratio' => (float) env('MEILISEARCH_SEMANTIC_RATIO', 0.7),
        'embedder_name' => env('MEILISEARCH_EMBEDDER_NAME', 'default'),
    ],

];
```

## `.env`

```env
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=masterKey
MEILISEARCH_INDEX_PREFIX=
MEILISEARCH_DOCUMENT_CHUNKS_INDEX=document_chunks
MEILISEARCH_SEMANTIC_RATIO=0.7
MEILISEARCH_EMBEDDER_NAME=default

OPENAI_BASE_URL=https://api.openai.com/v1
OPENAI_EMBEDDING_MODEL=text-embedding-3-small
```

---

# 7) Sync job: split doc, diff hashes, reindex changed chunks only

## `app/Jobs/SyncDocumentChunksJob.php`

```php
<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\Embeddings\EmbeddingService;
use App\Services\Embeddings\TextChunker;
use App\Services\Search\MeilisearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SyncDocumentChunksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $documentId)
    {
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping("sync-document-chunks-{$this->documentId}"))->expireAfter(300),
        ];
    }

    public function handle(
        TextChunker $chunker,
        EmbeddingService $embeddingService,
        MeilisearchService $meilisearch
    ): void {
        $document = Document::query()
            ->with('user', 'chunks')
            ->find($this->documentId);

        if (! $document) {
            return;
        }

        $meilisearch->ensureDocumentChunksIndex();

        $user = $document->user;
        $chunks = $chunker->chunk($document->searchableContent());

        $existingByIndex = $document->chunks->keyBy('chunk_index');

        $upserts = [];
        $searchDocuments = [];
        $seenIndexes = [];

        DB::transaction(function () use (
            $chunks,
            $existingByIndex,
            $document,
            $user,
            $embeddingService,
            &$upserts,
            &$searchDocuments,
            &$seenIndexes
        ): void {
            foreach ($chunks as $index => $content) {
                $seenIndexes[] = $index;
                $hash = hash('sha256', $content);

                /** @var DocumentChunk|null $existing */
                $existing = $existingByIndex->get($index);

                if ($existing && $existing->content_hash === $hash) {
                    $existing->loadMissing('document');

                    $searchDocuments[] = $existing->toSearchDocument(null);
                    continue;
                }

                $chunk = DocumentChunk::query()->updateOrCreate(
                    [
                        'document_id' => $document->id,
                        'chunk_index' => $index,
                    ],
                    [
                        'user_id' => $document->user_id,
                        'content' => $content,
                        'content_hash' => $hash,
                        'content_length' => mb_strlen($content),
                    ]
                );

                $chunk->setRelation('document', $document);

                $embedding = null;

                if ($user && $user->hasAiSearchEnabled()) {
                    $embedding = $embeddingService->embed($content, $user->ai_api_key);
                }

                $searchDocuments[] = $chunk->toSearchDocument($embedding);
                $upserts[] = $chunk;
            }
        });

        $staleChunks = DocumentChunk::query()
            ->where('document_id', $document->id)
            ->when($seenIndexes !== [], function ($query) use ($seenIndexes) {
                $query->whereNotIn('chunk_index', $seenIndexes);
            }, function ($query) {
                $query->whereRaw('1 = 1');
            })
            ->get();

        if ($staleChunks->isNotEmpty()) {
            $meilisearch->deleteDocuments(
                $staleChunks->map(fn (DocumentChunk $chunk) => $chunk->meilisearchId())->all()
            );

            DocumentChunk::query()
                ->whereIn('id', $staleChunks->pluck('id'))
                ->delete();
        }

        $toUpsert = collect($searchDocuments)
            ->filter(function (array $doc): bool {
                return array_key_exists('_vectors', $doc) || array_key_exists('content', $doc);
            })
            ->map(function (array $doc) use ($user): array {
                if (! ($user && $user->hasAiSearchEnabled())) {
                    unset($doc['_vectors']);
                }

                return $doc;
            })
            ->values()
            ->all();

        $meilisearch->upsertDocuments($toUpsert);

        if (! ($user && $user->hasAiSearchEnabled())) {
            $unchangedChunks = $existingByIndex
                ->filter(fn (DocumentChunk $chunk) => in_array($chunk->chunk_index, $seenIndexes, true))
                ->values();

            if ($unchangedChunks->isNotEmpty()) {
                $documentsWithoutVectors = $unchangedChunks
                    ->map(function (DocumentChunk $chunk) use ($document): array {
                        $chunk->setRelation('document', $document);
                        return $chunk->toSearchDocument(null);
                    })
                    ->all();

                $meilisearch->upsertDocuments($documentsWithoutVectors);
            }
        }
    }
}
```

That job:

* chunks the document
* hashes each chunk
* only embeds changed chunks
* deletes stale chunks
* re-upserts unchanged chunks without vectors when AI is disabled

---

# 8) Search service with grouping by document

This searches the chunk index, then groups hits by `document_id`.

## `app/Services/Search/DocumentChunkSearchService.php`

```php
<?php

namespace App\Services\Search;

use App\Models\User;
use App\Services\Embeddings\EmbeddingService;

class DocumentChunkSearchService
{
    public function __construct(
        protected EmbeddingService $embeddingService,
        protected MeilisearchService $meilisearchService
    ) {
    }

    public function search(User $user, string $query, int $limit = 20): array
    {
        $index = $this->meilisearchService->documentChunksIndex();

        $options = [
            'limit' => max(1, min($limit, 100)),
            'filter' => ['user_id = ' . $user->id],
        ];

        if ($user->hasAiSearchEnabled()) {
            $embedding = $this->embeddingService->embed($query, $user->ai_api_key);

            $options['vector'] = $embedding;
            $options['hybrid'] = [
                'semanticRatio' => (float) config('services.meilisearch.semantic_ratio', 0.7),
                'embedder' => config('services.meilisearch.embedder_name', 'default'),
            ];
        }

        $response = $index->search($query, $options);
        $hits = $response->getHits();

        $grouped = collect($hits)
            ->groupBy('document_id')
            ->map(function ($items, $documentId) {
                $items = collect($items)->values();

                $top = $items->first();

                return [
                    'document_id' => (int) $documentId,
                    'document_title' => $top['document_title'] ?? null,
                    'document_metadata' => $top['document_metadata'] ?? [],
                    'matched_chunks' => $items->take(3)->map(function ($hit) {
                        return [
                            'document_chunk_id' => $hit['document_chunk_id'] ?? null,
                            'chunk_index' => $hit['chunk_index'] ?? null,
                            'content' => $hit['content'] ?? '',
                        ];
                    })->values()->all(),
                    'match_count' => $items->count(),
                ];
            })
            ->values()
            ->all();

        return [
            'query' => $query,
            'hits' => $grouped,
            'raw_hits_count' => count($hits),
            'processing_time_ms' => $response->getProcessingTimeMs(),
            'estimated_total_hits' => method_exists($response, 'getEstimatedTotalHits')
                ? $response->getEstimatedTotalHits()
                : null,
        ];
    }
}
```

---

# 9) Search controller

## `app/Http/Controllers/DocumentSearchController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Services\Search\DocumentChunkSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentSearchController extends Controller
{
    public function __construct(
        protected DocumentChunkSearchService $searchService
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:2000'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $result = $this->searchService->search(
            $request->user(),
            $data['q'],
            $data['limit'] ?? 20
        );

        return response()->json($result);
    }
}
```

---

# 10) Controller for documents

## `app/Http/Controllers/DocumentController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ]);

        $document = Document::query()->create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);

        return response()->json($document, 201);
    }

    public function update(Request $request, Document $document): JsonResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'body' => ['sometimes', 'nullable', 'string'],
            'metadata' => ['sometimes', 'nullable', 'array'],
        ]);

        $document->fill($data);
        $document->save();

        return response()->json($document);
    }

    public function destroy(Request $request, Document $document): JsonResponse
    {
        abort_unless($document->user_id === $request->user()->id, 403);

        $chunkIds = $document->chunks()
            ->get()
            ->map(fn ($chunk) => $chunk->meilisearchId())
            ->all();

        app(\App\Services\Search\MeilisearchService::class)->deleteDocuments($chunkIds);

        $document->delete();

        return response()->json([
            'message' => 'Document deleted.',
        ]);
    }
}
```

---

# 11) Reindex command

## `app/Console/Commands/ReindexUserDocumentsCommand.php`

```php
<?php

namespace App\Console\Commands;

use App\Jobs\SyncDocumentChunksJob;
use App\Models\Document;
use App\Models\User;
use Illuminate\Console\Command;

class ReindexUserDocumentsCommand extends Command
{
    protected $signature = 'search:reindex-user-documents {user_id : The user ID}';
    protected $description = 'Queue chunk reindexing for all documents owned by a user';

    public function handle(): int
    {
        $user = User::query()->find($this->argument('user_id'));

        if (! $user) {
            $this->error('User not found.');

            return self::FAILURE;
        }

        Document::query()
            ->where('user_id', $user->id)
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($documents): void {
                foreach ($documents as $document) {
                    SyncDocumentChunksJob::dispatch($document->id);
                }
            });

        $this->info("Queued reindex for user {$user->id}.");

        return self::SUCCESS;
    }
}
```

---

# 12) Routes

## `routes/api.php`

```php
<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentSearchController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::patch('/documents/{document}', [DocumentController::class, 'update']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

    Route::get('/documents/search', DocumentSearchController::class);
});
```

---

# 13) Service provider bindings

## `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use App\Services\Embeddings\EmbeddingService;
use App\Services\Embeddings\OpenAiEmbeddingService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmbeddingService::class, OpenAiEmbeddingService::class);
    }

    public function boot(): void
    {
        //
    }
}
```

---

# 14) User model reminder

This assumes your `User` model already has:

* `ai_provider`
* `ai_api_key` encrypted via cast/accessor
* `ai_search_enabled`
* `hasAiSearchEnabled()`

Example helper:

## `app/Models/User.php`

```php
public function hasAiSearchEnabled(): bool
{
    return $this->ai_search_enabled
        && filled($this->ai_provider)
        && filled($this->ai_api_key);
}
```

---

# 15) Queue worker

You’ll need queues running, because embedding large docs inline would be rough.

```bash
php artisan queue:work
```

---

# 16) What happens with large documents?

For a 30–100 page document:

* the document is chunked into many smaller pieces
* each chunk gets its own embedding
* only modified chunks are re-embedded later
* search returns the most relevant chunks, not a muddy whole-document vector

That’s the correct retrieval design.

---

# 17) A few production improvements I’d add next

## A. Add chunk titles / section labels

If your source docs have headings, store them per chunk. Search results become much nicer.

## B. Store a `document_content_hash`

If the whole document hasn’t changed, skip chunking entirely.

## C. Batch embedding requests

If your provider supports embedding multiple inputs in one request, batch chunks to reduce request overhead.

## D. Add retry/backoff

Provider failures are normal. Set queue retries and exponential backoff.

## E. Keep chunk size provider-aware

Different embedding models have different token limits. A token-aware chunker is even better than char-based chunking.

---

# 18) One important caveat

The **Laravel structure is solid**, but the exact Meilisearch vector field names and hybrid payload shape can vary a bit by Meilisearch version.

The places to verify against your exact Meilisearch version are:

* `_vectors`
* `vector`
* `hybrid`
* `hybrid.embedder`

That’s not an architecture problem — just a version-specific API detail.

---

# 19) My recommendation for your app

For your use case, I would use:

* **one shared Meilisearch chunk index**
* filter by `user_id`
* user-owned API keys for embeddings
* queued chunk syncing
* grouped results by `document_id`

That avoids per-user indexes and keeps costs correctly attached to each user’s AI account.

---

# 20) One bug/optimization I’d change in the sync job

In the current `SyncDocumentChunksJob`, unchanged chunks are still re-upserted to Meilisearch to preserve index state. That is safe, but for very large corpora it creates more writes than needed.

A more optimized version would:

* only upsert changed chunks
* only upsert unchanged chunks when AI state changed
* track `embedded_with_provider`, `embedding_model`, or `vector_state` in DB

That’s the next refinement I’d do once the baseline is working.

---

If you want, next I can give you the **fully optimized v2** with:

* document-level hashing
* batch embeddings
* chunk excerpt/snippet generation
* provider failure handling
* cleaner state tracking so unchanged chunks are not re-upserted unnecessarily
