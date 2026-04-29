<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Tags\Tag as SpatieTag;

/**
 * @property-read int $id
 * @property int $user_id
 * @property string $slug
 * @property string $name
 * @property string $type
 * @property int $order_column
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 */
#[UseFactory(TagFactory::class)]
final class Tag extends SpatieTag implements UserModelInterface
{
    use BelongsToUser;
    use SoftDeletes;
}
