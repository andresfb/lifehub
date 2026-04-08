<?php

declare(strict_types=1);

namespace App\Domain\Core\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property string $name
 * @property string $url
 * @property bool $active
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
final class SearchProvider extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'core_search_providers';

    #[Override]
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }
}
