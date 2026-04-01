<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface AccountModelInterface
{
    public static function bootBelongsToAccount(): void;

    public function account(): BelongsTo;

    public function getAccountId(): string;

    public function setAccountId(string $accountId): void;
}
