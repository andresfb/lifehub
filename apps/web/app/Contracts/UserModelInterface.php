<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface UserModelInterface
{
    public static function bootBelongsToUser(): void;

    public function user(): BelongsTo;

    public function getUserId(): ?int;

    public function setUserId(int $userId): void;
}
