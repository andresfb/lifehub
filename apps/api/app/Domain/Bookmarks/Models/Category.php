<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Models;

use App\Contracts\UserModelInterface;
use App\Domain\Bookmarks\Observers\CategoryObserver;
use App\Domain\Bookmarks\Policies\CategoryPolicy;
use App\Enums\ModuleKey;
use App\Models\User;
use App\Traits\BelongsToUser;
use App\Traits\SlugOptionable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Override;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string $slug
 * @property string $title
 * @property bool $active
 * @property int $order
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 */
#[ObservedBy([CategoryObserver::class])]
#[UsePolicy(CategoryPolicy::class)]
final class Category extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
    use HasSlug;
    use SlugOptionable;
    use SoftDeletes;

    protected $table = 'bookmarks_categories';

    public static function getSelectableList(int $userId): array
    {
        return Cache::tags("categories:{$userId}")
            ->remember(
                "selectable:list:{$userId}",
                now()->addHours(5),
                function () use ($userId): array {
                    return self::query()
                        ->where('user_id', $userId)
                        ->where('active', true)
                        ->orderBy('order')
                        ->pluck('title', 'id')
                        ->toArray();
                }
            );
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return $this->loadSlugOptions('title', ModuleKey::BOOKMARKS->value);
    }

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
