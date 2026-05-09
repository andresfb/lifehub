<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use App\Contracts\UserModelInterface;
use App\Models\User;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $module
 * @property string $type
 * @property string $hash
 * @property string $query
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read User $user
 */
final class SearchHistory extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
    use Searchable;

    public function searchableAs(): string
    {
        return 'search_history_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'module' => $this->module,
            'type' => $this->type,
            'query' => $this->query,
        ];
    }
}
