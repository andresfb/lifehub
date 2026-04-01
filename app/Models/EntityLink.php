<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Contracts\AccountModelInterface;
use App\Traits\BelongsToAccount;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
class EntityLink extends Model implements AccountModelInterface
{
    use HasFactory;
    use BelongsToAccount;
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;
}
