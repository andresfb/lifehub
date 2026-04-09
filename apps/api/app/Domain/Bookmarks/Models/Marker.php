<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Models;

use App\Contracts\GlobalSearchInterface;
use App\Contracts\UserModelInterface;
use App\Domain\Bookmarks\Enums\MarkerStatus;
use App\Domain\Bookmarks\Enums\MorphTypes;
use App\Domain\Bookmarks\Libraries\MediaNamesLibrary;
use App\Domain\Bookmarks\Observers\MarkerObserver;
use App\Domain\Bookmarks\Policies\MarkerPolicy;
use App\Enums\ModuleKey;
use App\Models\Tag;
use App\Models\User;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use Override;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;

/**
 * @property-read int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $hash
 * @property string $status
 * @property string $url
 * @property string|null $title
 * @property string|null $domain
 * @property string|null $description
 * @property string|null $summary
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
final class Marker extends Model implements Auditable, GlobalSearchInterface, HasMedia, UserModelInterface
{
    use AuditableTrait;
    use BelongsToUser;
    use HasFactory;
    use HasTags;
    use InteractsWithMedia;
    use Searchable;
    use SoftDeletes;

    protected $table = 'bookmarks_markers';

    public static function getHash(string $url, int $userId): string
    {
        return md5(sprintf(
            '%s:%s',
            trim($url),
            $userId,
        ));
    }

    public static function found(string $url, int $userId): bool
    {
        $urlHash = self::getHash($url, $userId);

        return Cache::tags("markers:{$userId}")
            ->remember(
                $urlHash,
                now()->addMonth(),
                function () use ($urlHash): bool {
                    return self::query()
                        ->where('hash', $urlHash)
                        ->exists();
                }
            );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaNamesLibrary::screenshot())
            ->singleFile()
            ->acceptsMimeTypes([
                'image/png',
                'image/jpeg',
                'image/webp',
            ])
            ->withResponsiveImages();
    }

    public function getIdentifier(): string
    {
        return str(ModuleKey::BOOKMARKS->value)
            ->append(':')
            ->append('marker')
            ->append(':')
            ->append($this->id)
            ->lower()
            ->toString();
    }

    public function getTags(): array
    {
        if (blank($this->tags)) {
            return [];
        }

        return $this->tags
            ->pluck('name')
            ->values()
            ->all();
    }

    public function searchableAs(): string
    {
        return 'bookmarks_markers_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string) $this->id,
            'user_id' => (string) $this->user_id,
            'category_id' => (string) $this->category_id,
            'status' => $this->status->value,
            'category' => $this->category->title,
            'title' => $this->title,
            'url' => $this->url,
            'domain' => $this->domain ?? '',
            'description' => $this->description ?? '',
            'summary' => $this->summary ?? '',
            'notes' => $this->notes ?? '',
            'deleted_at' => $this->deleted_at?->unix(),
            'created_at' => $this->created_at?->unix(),
            'updated_at' => $this->updated_at?->unix() ?? now()->unix(),
            'tags' => $this->getTags(),
        ];
    }

    public function buildGlobalSearch(): array
    {
        return [
            'creator_id' => $this->getIdentifier(),
            'user_id' => (string) $this->user_id,
            'entity_type' => MorphTypes::BOOKMARKS_MARKER->name,
            'entity_id' => (string) $this->id,
            'module' => 'BOOKMARKS',
            'title' => $this->title,
            'body' => $this->parseBody(),
            'tags' => $this->getTags(),
            'keywords' => [],
            'metadata' => [
                'icon' => 'link',
            ],
            'urls' => [
                'web_route' => 'bookmarks.marker.show',
                'api_route' => 'api.v1.bookmarks.marker.show',
            ],
            'is_private' => false,
            'is_archived' => $this->status === MarkerStatus::ARCHIVED->value,
            'source_updated_at' => $this->updated_at,
        ];
    }

    protected static function booted(): void
    {
        self::addGlobalScope(static function (Builder $builder) {
            $builder->with('category')
                ->with('user')
                ->with('tags');
        });
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', MarkerStatus::ACTIVE)
            ->orderBy('priority')
            ->latest();
    }

    #[Scope]
    protected function archived(Builder $query): Builder
    {
        return $query->where('status', MarkerStatus::ARCHIVED)
            ->orderBy('priority')
            ->latest();
    }

    #[Scope]
    protected function hidden(Builder $query): Builder
    {
        return $query->where('status', MarkerStatus::HIDDEN)
            ->orderBy('priority');
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
            ->append($this->title ?? '')
            ->trim()
            ->newLine(2)
            ->append($this->url)
            ->newLine(2)
            ->append($this->parseDescription())
            ->trim()
            ->newLine(2)
            ->append($this->parseSummary())
            ->trim()
            ->newLine(2)
            ->append($this->parseNotes())
            ->trim()
            ->toString();
    }

    private function parseDescription(): string
    {
        if (blank($this->description)) {
            return '';
        }

        return str('DESCRIPTION')
            ->newLine(2)
            ->append($this->description)
            ->trim()
            ->toString();
    }

    private function parseSummary(): string
    {
        if (blank($this->summary)) {
            return '';
        }

        return str('SUMMARY')
            ->newLine(2)
            ->append($this->summary)
            ->trim()
            ->toString();
    }

    private function parseNotes(): string
    {
        if (blank($this->notes)) {
            return '';
        }

        return str('NOTES')
            ->newLine(2)
            ->append($this->notes)
            ->trim()
            ->toString();
    }
}
