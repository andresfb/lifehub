<?php

declare(strict_types=1);

namespace Modules\Dashboard\Models;

use App\Contracts\UserModelInterface;
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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\Dashboard\Database\Factories\HomepageSectionFactory;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionResource;
use Modules\Dashboard\Observers\HomepageSectionObserver;
use Modules\Dashboard\Policies\HomepageSectionPolicy;
use Override;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $slug
 * @property string $name
 * @property bool $active
 * @property int $order
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Collection<int, HomepageItem> $items
 */
#[Table(name: 'dashboard_homepage_sections')]
#[UseFactory(HomepageSectionFactory::class)]
#[ObservedBy(HomepageSectionObserver::class)]
#[UsePolicy(HomepageSectionPolicy::class)]
#[UseResource(HomepageSectionResource::class)]
final class HomepageSection extends Model implements UserModelInterface
{
    use BelongsToUser;
    use CascadeSoftDeletes;
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    /** @var array<int, string> */
    protected array $cascadeDeletes = ['items'];

    /**
     * @return Collection<int, HomepageSection>
     */
    public static function getUserSections(int $userId, int $status): Collection
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('active', true)
            ->withItemsByStatus($status)
            ->orderBy('order')
            ->get();
    }

    /**
     * @return HasMany<HomepageItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(HomepageItem::class, 'section_id')
            ->orderBy('order');
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<self>  $query
     */
    protected function scopeWithItemsByStatus(Builder $query, int $status): void
    {
        $query->withWhereHas('items', function ($items) use ($status): void {
            if ($status !== -1) {
                $items->where('active', (bool) $status);
            }

            $items->with('tags');
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
