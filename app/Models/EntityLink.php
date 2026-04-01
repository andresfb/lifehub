<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\AccountModelInterface;
use App\Traits\BelongsToAccount;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read string $id
 * @property-read string $account_id
 * @property-read string $source_id
 * @property-read string $source_type
 * @property-read string $target_id
 * @property-read string $target_type
 * @property-read string $relation_type
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
final class EntityLink extends Model implements AccountModelInterface
{
    use BelongsToAccount;
    use HasFactory;
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';
}
