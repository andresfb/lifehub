<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AccountType;
use Carbon\CarbonImmutable;
use Database\Factories\AccountUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Override;

/**
 * @property-read int $id
 * @property-read int $account_id
 * @property-read int $user_id
 * @property-read AccountType $role
 * @property-read CarbonImmutable|null $deleted_at
 * @property-read CarbonImmutable|null $created_at
 * @property-read CarbonImmutable|null $updated_at
 * @property-read Account $account
 * @property-read User $user
 */
final class AccountUser extends Model
{
    /** @use HasFactory<AccountUserFactory> */
    use HasFactory;

    use SoftDeletes;

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Override]
    protected function casts(): array
    {
        return [
            'role' => AccountType::class,
        ];
    }
}
