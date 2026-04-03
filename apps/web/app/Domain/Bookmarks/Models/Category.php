<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Models;

use App\Domain\Bookmarks\Contracts\OwnedModelInterface;
use App\Domain\Bookmarks\Policies\CategoryPolicy;
use App\Domain\Bookmarks\Traits\SlugOptionable;
use App\Domain\Bookmarks\Traits\UserIdiable;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string $slug
 * @property string $title
 * @property int $order_by
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 */
#[UsePolicy(CategoryPolicy::class)]
final class Category extends Model implements OwnedModelInterface
{
    use HasFactory;
    use HasSlug;
    use SlugOptionable;
    use SoftDeletes;
    use UserIdiable;

    protected $table = 'bookmarks_categories';

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
}
