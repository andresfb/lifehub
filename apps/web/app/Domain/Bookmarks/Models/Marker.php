<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Models;

use App\Contracts\GlobalSearchInterface;
use App\Contracts\UserModelInterface;
use App\Domain\Bookmarks\Enums\MarkerStatus;
use App\Domain\Bookmarks\Enums\MorphTypes;
use App\Domain\Bookmarks\Observers\MarkerObserver;
use App\Domain\Bookmarks\Policies\MarkerPolicy;
use App\Domain\Bookmarks\Traits\SlugOptionable;
use App\Models\Tag;
use App\Models\User;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read int $category_id
 * @property-read string $slug
 * @property string $title
 * @property string|null $site_title
 * @property string $status
 * @property string $url
 * @property string|null $description
 * @property string|null $notes
 * @property int $priority
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Category $category
 * @property-read Collection<Tag> $tags
 * @property-read Collection<Audit> $audits
 */
#[ObservedBy([MarkerObserver::class])]
#[UsePolicy(MarkerPolicy::class)]
final class Marker extends Model implements Auditable, GlobalSearchInterface, UserModelInterface
{
    use AuditableTrait;
    use BelongsToUser;
    use HasFactory;
    use HasSlug;
    use HasTags;
    use SlugOptionable;
    use SoftDeletes;

    protected $table = 'bookmarks_markers';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return $this->loadSlugOptions('title');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getIdentifier(): string
    {
        return "marker:{$this->id}";
    }

    public function buildGlobalSearch(): array
    {
        return [
            'id' => $this->getIdentifier(),
            'user_id' => (string) $this->user_id,
            'entity_type' => MorphTypes::BOOKMARKS_MARKER->name,
            'entity_id' => (string) $this->id,
            'module' => 'BOOKMARKS',
            'title' => $this->title,
            'body' => $this->parseBody(),
            'tags' => $this->tags?->pluck('name')->values()->all() ?? [],
            'keywords' => [],
            'metadata' => [
                'icon' => 'link',
            ],
            'urls' => [
                //                'web' => route('marker.show', $this),
                //                'api' => route('api.v1.marker.show', $this),
            ],
            'is_private' => false,
            'is_archived' => $this->status === MarkerStatus::ARCHIVED->value,
            'source_updated_at' => $this->updated_at,
        ];
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'status' => MarkerStatus::class,
        ];
    }

    private function parseBody(): string
    {
        return str($this->category->title)
            ->newLine(2)
            ->append($this->site_title ?? '')
            ->newLine(2)
            ->append($this->domain ?? '')
            ->trim()
            ->newLine(2)
            ->append($this->url)
            ->newLine(2)
            ->append($this->description ?? '')
            ->newLine(2)
            ->append($this->notes ?? '')
            ->trim()
            ->toString();
    }
}
