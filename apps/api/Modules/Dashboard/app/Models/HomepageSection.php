<?php

declare(strict_types=1);

namespace Modules\Dashboard\Models;

use App\Contracts\UserModelInterface;
use App\Enums\ModuleKey;
use App\Models\User;
use App\Traits\BelongsToUser;
use App\Traits\HasSlug;
use Carbon\CarbonImmutable;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionResource;
use Modules\Dashboard\Observers\HomepageSectionObserver;
use Modules\Dashboard\Policies\HomepageSectionPolicy;
use Override;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property string $slug
 * @property string $name
 * @property bool $active
 * @property int $order
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 * @property-read Collection<HomepageItem> $items
 */
#[Table(name: 'dashboard_homepage_sections')]
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

    protected array $cascadeDeletes = ['items'];

    public static function getUserSections(int $userId): Collection
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('active', true)
            ->with('items.tags')
            ->orderBy('order')
            ->get();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<HomepageItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(HomepageItem::class, 'section_id')
            ->where('active', true)
            ->orderBy('order');
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'active' => 'bool',
        ];
    }
}
