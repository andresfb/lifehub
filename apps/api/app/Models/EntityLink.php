<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Database\Factories\EntityLinkFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property int $user_id
 * @property int $source_id
 * @property string $source_type
 * @property int $target_id
 * @property string $target_type
 * @property string $relation_type
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 */
#[UseFactory(EntityLinkFactory::class)]
final class EntityLink extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;
}
