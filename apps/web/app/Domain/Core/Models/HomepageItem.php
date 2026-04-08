<?php

declare(strict_types=1);

namespace App\Domain\Core\Models;

use App\Contracts\GlobalSearchInterface;
use App\Contracts\UserModelInterface;
use App\Domain\Core\Enums\MorphTypes;
use App\Domain\Core\Libraries\MediaNamesLibrary;
use App\Domain\Core\Observers\HomepageItemObserver;
use App\Domain\Core\Policies\HomepageItemPolicy;
use App\Enums\ModuleKey;
use App\Models\User;
use App\Traits\BelongsToUser;
use App\Traits\SlugOptionable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read int $homepage_section_id
 * @property string $slug
 * @property string $title
 * @property string $url
 * @property string $bg_color
 * @property bool $active
 * @property int $order
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read HomepageSection $section
 */
#[ObservedBy([HomepageItemObserver::class])]
#[UsePolicy(HomepageItemPolicy::class)]
final class HomepageItem extends Model implements HasMedia, UserModelInterface, GlobalSearchInterface
{
    use BelongsToUser;
    use HasFactory;
    use HasSlug;
    use HasTags;
    use InteractsWithMedia;
    use SlugOptionable;
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'core_homepage_items';

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
        return $this->belongsTo(HomepageSection::class, 'homepage_section_id');
    }

    public function getSlugOptions(): SlugOptions
    {
        return $this->loadSlugOptions('title', ModuleKey::CORE->value);
    }

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
        return sprintf(
            '%s:homepage_item:%d',
            ModuleKey::CORE->value,
            $this->id
        );
    }

    public function buildGlobalSearch(): array
    {
        return [
            'creator_id' => $this->getIdentifier(),
            'user_id' => (string) $this->user_id,
            'entity_type' => MorphTypes::CORE_HOMEPAGE_ITEM->name,
            'entity_id' => (string) $this->id,
            'module' => 'CORE',
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
                'web_route' => 'homepage.item.show',
                'api_route' => 'api.v1.homepage.item.show',
            ],
            'is_private' => false,
            'is_archived' => ! $this->active,
            'source_updated_at' => $this->updated_at,
        ];
    }

    protected static function booted(): void
    {
        self::addGlobalScope(static function (Builder $builder) {
            $builder->with('section')
                ->with('user')
                ->with('tags');
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
