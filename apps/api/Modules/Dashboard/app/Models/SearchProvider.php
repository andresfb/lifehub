<?php

declare(strict_types=1);

namespace Modules\Dashboard\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Dashboard\Observers\SearchProviderObserver;
use Modules\Dashboard\Policies\SearchProviderPolicy;
use Override;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property string $name
 * @property string $url
 * @property bool $active
 * @property int $order
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[ObservedBy([SearchProviderObserver::class])]
#[UsePolicy(SearchProviderPolicy::class)]
#[Table(name: 'dashboard_search_providers')]
final class SearchProvider extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
    use SoftDeletes;

    #[Override]
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }
}
