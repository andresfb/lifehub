<?php

declare(strict_types=1);

namespace Modules\Dashboard\Models;

use App\Contracts\GlobalSearchInterface;
use App\Contracts\UserModelInterface;
use App\Enums\ModuleKey;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use App\Traits\BelongsToUser;
use App\Traits\SlugOptionable;
use Carbon\CarbonImmutable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\Dashboard\Enums\MorphTypes;
use Modules\Dashboard\Libraries\MediaNamesLibrary;
use Modules\Dashboard\Observers\HomepageItemObserver;
use Modules\Dashboard\Policies\HomepageItemPolicy;
use Override;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read int $section_id
 * @property string $slug
 * @property string $title
 * @property string $url
 * @property string $description
 * @property string $bg_color
 * @property bool $active
 * @property int $order
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read HomepageSection $section
 * @property-read Collection<Tag> $tags
 * @property-read Collection<Media> $media
 */
#[ObservedBy([HomepageItemObserver::class])]
#[UsePolicy(HomepageItemPolicy::class)]
#[Table(name: 'dashboard_homepage_items')]
final class HomepageItem extends Model implements GlobalSearchInterface, HasMedia, UserModelInterface
{
    use BelongsToUser;
    use CascadeSoftDeletes;
    use HasFactory;
    use HasSlug;
    use HasTags;
    use InteractsWithMedia;
    use SlugOptionable;
    use SoftDeletes;

    protected array $cascadeDeletes = ['tags', 'media'];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<HomepageSection, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(HomepageSection::class, 'section_id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return $this->loadSlugOptions('title', ModuleKey::DASHBOARD->value);
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(MediaNamesLibrary::icon())
            ->singleFile()
            ->acceptsMimeTypes([
                'image/png',
                'image/jpeg',
                'image/webp',
                'image/svg+xml',
                'image/x-icon',
            ])
            ->useDisk('s3_open');
    }

    public function getIcon(): ?string
    {
        if (! $this->hasMedia(MediaNamesLibrary::icon())) {
            return null;
        }

        return $this->getFirstMediaUrl(MediaNamesLibrary::icon());
    }

    public function getTags(): ?array
    {
        if (blank($this->tags)) {
            return null;
        }

        return $this->tags
            ->pluck('name')
            ->values()
            ->all();
    }

    public function getIdentifier(): string
    {
        return str(ModuleKey::DASHBOARD->value)
            ->append(':')
            ->append('homepage_item')
            ->append(':')
            ->append($this->id)
            ->lower()
            ->toString();
    }

    public function buildGlobalSearch(): array
    {
        return [
            'creator_id' => $this->getIdentifier(),
            'user_id' => (string) $this->user_id,
            'entity_type' => MorphTypes::DASHBOARD_HOMEPAGE_ITEM->name,
            'entity_id' => (string) $this->id,
            'module' => ModuleKey::DASHBOARD->name,
            'title' => $this->title,
            'body' => str($this->section->name)
                ->newLine(2)
                ->append($this->title)
                ->newLine(2)
                ->append($this->url),
            'tags' => $this->getTags(),
            'keywords' => [],
            'metadata' => [
                'icon' => 'link',
            ],
            'urls' => [
                'api_route' => 'api.v1.bookmarks.show',
            ],
            'is_private' => false,
            'is_archived' => ! $this->active,
            'source_updated_at' => $this->updated_at,
        ];
    }

    #[Override]
    protected static function booted(): void
    {
        self::addGlobalScope(static function (Builder $builder) {
            $builder->with('section')
                ->with('user')
                ->with('tags')
                ->with('media');
        });
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'active' => 'bool',
        ];
    }
}
