<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Models;

use App\Contracts\UserModelInterface;
use App\Domain\Bookmarks\Observers\CategoryObserver;
use App\Domain\Bookmarks\Policies\CategoryPolicy;
use App\Domain\Bookmarks\Traits\SlugOptionable;
use App\Models\User;
use App\Traits\BelongsToUser;
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
 * @property int $order_by
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

    public static function getSelectableList(): array
    {
        return Cache::tags('categories')
            ->remember(
                'selectable:list',
                now()->addHours(5),
                function (): array {
                    return self::query()
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
        return $this->loadSlugOptions('title');
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
