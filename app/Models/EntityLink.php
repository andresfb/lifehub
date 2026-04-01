<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\AccountModelInterface;
use App\Traits\BelongsToAccount;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read int $account_id
 * @property-read int $source_id
 * @property-read string $source_type
 * @property-read int $target_id
 * @property-read string $target_type
 * @property-read string $relation_type
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 */
final class EntityLink extends Model implements AccountModelInterface
{
    use BelongsToAccount;
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';
}
