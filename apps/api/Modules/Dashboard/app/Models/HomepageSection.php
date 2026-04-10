<?php

declare(strict_types=1);

namespace Modules\Dashboard\Models;

use App\Contracts\UserModelInterface;
use App\Enums\ModuleKey;
use App\Models\User;
use App\Traits\BelongsToUser;
use App\Traits\SlugOptionable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Dashboard\Observers\HomepageSectionObserver;
use Modules\Dashboard\Policies\HomepageSectionPolicy;
use Override;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

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
#[ObservedBy(HomepageSectionObserver::class)]
#[UsePolicy(HomepageSectionPolicy::class)]
#[Guarded(['id'])]
#[Table(name: 'dashboard_homepage_sections')]
final class HomepageSection extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
    use HasSlug;
    use SlugOptionable;
    use SoftDeletes;

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

    public function getSlugOptions(): SlugOptions
    {
        return $this->loadSlugOptions('name', ModuleKey::DASHBOARD->value);
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
