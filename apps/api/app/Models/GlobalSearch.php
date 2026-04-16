<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Observers\GlobalSearchObserver;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Database\Factories\GlobalSearchFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

/**
 * @property-read int $id
 * @property-read string $creator_id
 * @property-read int $user_id
 * @property-read string $entity_type
 * @property-read int $entity_id
 * @property string $module
 * @property string $title
 * @property string $body
 * @property array<int|string, mixed> $tags
 * @property array<int|string, mixed> $keywords
 * @property array<int|string, mixed> $metadata
 * @property array<int|string, mixed> $urls
 * @property bool $is_private
 * @property bool $is_archived
 * @property-read CarbonImmutable|null $source_updated_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Collection<int, GlobalSearchChunk> $chunks
 */
#[ObservedBy([GlobalSearchObserver::class])]
#[UseFactory(GlobalSearchFactory::class)]
final class GlobalSearch extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;

    public function searchableAs(): string
    {
        return 'global_search_index';
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'creator_id' => $this->creator_id,
            'user_id' => (string) $this->user_id,
            'entity_type' => $this->entity_type,
            'entity_id' => (string) $this->entity_id,
            'module' => $this->module,
            'title' => $this->title ?? '',
            'body' => $this->body ?? '',
            'tags' => is_array($this->tags) ? $this->tags : [],
            'keywords' => is_array($this->keywords) ? $this->keywords : [],
            'urls' => is_array($this->urls) ? array_values($this->urls) : [],
            'is_private' => $this->is_private,
            'is_archived' => $this->is_archived,
            'created_at' => $this->created_at?->unix(),
            'updated_at' => $this->updated_at?->unix() ?? now()->unix(),
            'source_updated_at' => $this->source_updated_at?->unix(),
        ];
    }

    /**
     * @return HasMany<GlobalSearchChunk, $this>
     */
    public function chunks(): HasMany
    {
        return $this->hasMany(GlobalSearchChunk::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'keywords' => 'array',
            'urls' => 'array',
            'metadata' => 'array',
            'is_private' => 'boolean',
            'is_archived' => 'boolean',
            'source_updated_at' => 'datetime',
        ];
    }
}
