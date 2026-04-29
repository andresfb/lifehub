<?php

declare(strict_types=1);

namespace Modules\Dashboard\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Modules\Dashboard\Database\Factories\SearchProviderFactory;
use Modules\Dashboard\Http\Resources\Api\V1\SearchProviderResource;
use Modules\Dashboard\Observers\SearchProviderObserver;
use Modules\Dashboard\Policies\SearchProviderPolicy;
use Override;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $name
 * @property string $url
 * @property string $icon
 * @property string $icon_color
 * @property bool $active
 * @property bool $default
 * @property int $order
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
#[Table(name: 'dashboard_search_providers')]
#[UseFactory(SearchProviderFactory::class)]
#[ObservedBy([SearchProviderObserver::class])]
#[UsePolicy(SearchProviderPolicy::class)]
#[UseResource(SearchProviderResource::class)]
final class SearchProvider extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
    use SoftDeletes;

    /**
     * @return Collection<int, SearchProvider>
     */
    public static function getUserProviders(int $userId): Collection
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('active', true)
            ->orderByDesc('default')
            ->orderBy('order')
            ->get();
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'default' => 'boolean',
        ];
    }
}
