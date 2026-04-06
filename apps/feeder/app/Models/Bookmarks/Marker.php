<?php

namespace App\Models\Bookmarks;

use App\Models\Bookmarks\Tag as BookmarkTag;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\HasTags;

/**
 * @property-read int $id
 * @property-read string $status
 * @property-read string $url
 * @property-read string $title
 * @property-read string $domain
 * @property-read string $notes
 * @property-read int $priority
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class Marker extends Model
{
    use HasTags;
    use SoftDeletes;

    protected $connection = 'bookmarks';

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function getTagList(): array
    {
        if (empty($this->tags)) {
            return [];
        }

        return $this->tags->pluck('name')->toArray();
    }

    public function getMorphClass(): string
    {
        return 'App\Models\Marker';
    }

    public static function getTagClassName(): string
    {
        return BookmarkTag::class;
    }
}

