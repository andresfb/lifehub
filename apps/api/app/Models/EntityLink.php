<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read int $source_id
 * @property-read string $source_type
 * @property-read int $target_id
 * @property-read string $target_type
 * @property-read string $relation_type
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 */
final class EntityLink extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
}
