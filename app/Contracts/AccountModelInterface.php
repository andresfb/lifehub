<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface AccountModelInterface
{
    public static function bootBelongsToAccount(): void;

    public function account(): BelongsTo;

    public function getAccountId(): int;

    public function setAccountId(int $accountId): void;
}
