<?php

declare(strict_types=1);

namespace App\Traits;

use App\Contracts\UserModelInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Scopes a model to the authenticated user.
 *
 * Requires a `user_id` column on the model's table.
 *
 * @mixin Model
 */
trait BelongsToUser
{
    public static function bootBelongsToUser(): void
    {
        static::addGlobalScope('user', static function (Builder $builder): void {
            $userId = Auth::id();
            if (blank($userId)) {
                return;
            }

            $builder->where(sprintf(
                '%s.user_id',
                $builder->getModel()->getTable()),
                $userId
            );
        });

        static::creating(static function (UserModelInterface $model): void {
            if ($model->getUserId() || Auth::user() === null) {
                return;
            }

            $userId = Auth::id();
            if (blank($userId)) {
                return;
            }

            $model->setUserId($userId);
        });
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUserId(): ?int
    {
        if (blank($this->user_id)) {
            return null;
        }

        $userId = $this->user_id;
        if (is_string($userId)) {
            $userId = (int) $userId;
        }

        if (blank($userId)) {
            return null;
        }

        return $userId;
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }
}
