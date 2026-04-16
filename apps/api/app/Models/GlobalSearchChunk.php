<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\GlobalSearchChunkFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property-read int $id
 * @property int $global_search_id
 * @property int $user_id
 * @property int $chunk_index
 * @property string $content
 * @property string $content_hash
 * @property int $content_length
 * @property string|null $embedded_provider_code
 * @property string|null $embedded_model
 * @property int|null $embedded_dimensions
 * @property string|null $embedded_content_hash
 * @property CarbonImmutable|null $embedded_at
 * @property string|null $embedding_failed_reason
 * @property CarbonImmutable|null $embedding_failed_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read GlobalSearch $globalSearch
 * @property-read User $user
 */
#[UseFactory(GlobalSearchChunkFactory::class)]
final class GlobalSearchChunk extends Model
{
    use HasFactory;

    public function meilisearchId(): string
    {
        return "{$this->global_search_id}_{$this->chunk_index}";
    }

    /**
     * @param  array<int, float>|null  $embedding
     * @return array<string, mixed>
     */
    public function toSearchDocument(?array $embedding = null): array
    {
        $this->loadMissing('globalSearch');

        $globalSearch = $this->globalSearch;
        $embedder = config('search.hybrid.embedder', 'global_search_user_provided');
        $embedder = is_string($embedder) && $embedder !== '' ? $embedder : 'global_search_user_provided';

        $payload = [
            ...$globalSearch->toSearchableArray(),
            'id' => $this->meilisearchId(),
            'global_search_chunk_id' => $this->id,
            'global_search_id' => $this->global_search_id,
            'user_id' => $this->user_id,
            'chunk_index' => $this->chunk_index,
            'content' => $this->content,
            'content_hash' => $this->content_hash,
            'created_at' => $this->created_at?->unix(),
            'updated_at' => $this->updated_at?->unix(),
        ];

        if ($embedding !== null) {
            $payload['_vectors'] = [$embedder => $embedding];
        }

        return $payload;
    }

    /**
     * @return BelongsTo<GlobalSearch, $this>
     */
    public function globalSearch(): BelongsTo
    {
        return $this->belongsTo(GlobalSearch::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'chunk_index' => 'integer',
            'content_length' => 'integer',
            'embedded_dimensions' => 'integer',
            'embedded_at' => 'datetime',
            'embedding_failed_at' => 'datetime',
        ];
    }
}
