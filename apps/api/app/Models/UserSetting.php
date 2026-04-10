<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserModelInterface;
use App\Traits\BelongsToUser;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property string $code
 * @property string $key
 * @property string $value
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read User $user
 */
final class UserSetting extends Model implements UserModelInterface
{
    use BelongsToUser;
    use HasFactory;

    public static function getMenuShortcut(int $userId, ?string $code): ?string
    {
        if (blank($code)) {
            return null;
        }

        return self::getValue($userId, $code, 'shortcuts');
    }

    public static function getValue(int $userId, string $code, string $key): ?string
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('code', $code)
            ->where('key', $key)
            ->first()?->value;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
