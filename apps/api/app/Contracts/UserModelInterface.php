<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-require-extends Model
 */
interface UserModelInterface
{
    public static function bootBelongsToUser(): void;

    public function getUserId(): ?int;

    public function setUserId(int $userId): void;
}
