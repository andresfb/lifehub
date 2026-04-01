<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Override;

/**
 * @property int $id
 * @property int $user_id
 * @property string $entity_type
 * @property int $entity_id
 * @property string $module
 * @property string $title
 * @property string $body
 * @property array $tags
 * @property array $keywords
 * @property array $metadata
 * @property array $urls
 * @property bool $is_private
 * @property bool $is_archived
 * @property-read CarbonImmutable|null $source_updated_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 */
final class SearchItem extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
    use Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'user_id' => (string) $this->user_id,
            'entity_type' => $this->entity_type,
            'entity_id' => (string) $this->entity_id,
            'module' => $this->module,
            'title' => $this->title ?? '',
            'body' => $this->body ?? '',
            'tags' => $this->tags ?? [],
            'keywords' => $this->keywords ?? [],
            'urls' => $this->urls ?? [],
            'is_private' => $this->is_private,
            'is_archived' => $this->is_archived,
            'created_at' => $this->created_at?->timestamp,
            'updated_at' => $this->updated_at?->timestamp,
            'source_updated_at' => $this->source_updated_at?->timestamp,
        ];
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'keyboards' => 'array',
            'metadata' => 'array',
            'is_private' => 'boolean',
            'is_archived' => 'boolean',
            'source_updated_at' => 'timestamp',
        ];
    }
}
