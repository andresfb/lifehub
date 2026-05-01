<?php

declare(strict_types=1);

namespace Modules\Dashboard\Models;

use App\Contracts\UserModelInterface;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use App\Traits\BelongsToUser;
use App\Traits\HasSlug;
use Carbon\CarbonImmutable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;
use Modules\Dashboard\Database\Factories\HomepageItemFactory;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageItemResource;
use Modules\Dashboard\Observers\HomepageItemObserver;
use Modules\Dashboard\Policies\HomepageItemPolicy;
use Override;
use Spatie\Tags\HasTags;

/**
 * @property-read int $id
 * @property int $user_id
 * @property int $section_id
 * @property string $slug
 * @property string $title
 * @property string $url
 * @property string $description
 * @property string $icon
 * @property string $icon_color
 * @property bool $active
 * @property int $order
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read HomepageSection $section
 * @property-read Collection<int, Tag> $tags
 * @property-read Collection<int, Media> $media
 */
#[Table(name: 'dashboard_homepage_items')]
#[UseFactory(HomepageItemFactory::class)]
#[ObservedBy([HomepageItemObserver::class])]
#[UsePolicy(HomepageItemPolicy::class)]
#[UseResource(HomepageItemResource::class)]
final class HomepageItem extends Model implements UserModelInterface
{
    use BelongsToUser;
    use CascadeSoftDeletes;
    use HasFactory;
    use HasSlug;
    use HasTags;
    use Searchable;
    use SoftDeletes;

    /** @var array<int, string> */
    protected array $cascadeDeletes = ['tags'];

    public static function found(int $userId, string $url, int $sectionId): bool
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('section_id', $sectionId)
            ->where('url', $url)
            ->exists();
    }

    /**
     * @return BelongsTo<HomepageSection, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(HomepageSection::class, 'section_id');
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return array<int, string>|null
     */
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

    public function searchableAs(): string
    {
        return 'home_page_item_index';
    }

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        $this->load('tags');

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'section_id' => $this->section_id,
            'section' => $this->section->name,
            'title' => $this->title,
            'url' => $this->url,
            'tags' => $this->getTags(),
            'created_at' => $this->created_at,
        ];
    }

    #[Override]
    protected static function booted(): void
    {
        self::addGlobalScope(static function (Builder $builder) {
            $builder->with('section')
                ->with('user')
                ->with('tags');
        });
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'active' => 'bool',
        ];
    }
}
