<?php

declare(strict_types=1);

namespace App\Traits;

use App\Contracts\AccountModelInterface;
use App\Models\Account;
use App\Models\AccountUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Scopes a model to the authenticated user's account.
 *
 * Requires an `account_id` column on the model's table.
 *
 * @mixin Model
 */
trait BelongsToAccount
{
    public static function bootBelongsToAccount(): void
    {
        static::addGlobalScope('account', static function (Builder $builder): void {
            if ($user = Auth::user()) {
                $accountId = AccountUser::query()
                    ->where('user_id', $user->id)
                    ->value('account_id');

                if ($accountId) {
                    $builder->where(sprintf(
                        "%s.account_id",
                        $builder->getModel()->getTable()),
                        $accountId
                    );
                }
            }
        });

        static::creating(static function (AccountModelInterface $model): void {
            if (! $model->getAccountId() && ($user = Auth::user())) {
                $accountId = AccountUser::query()
                    ->where('user_id', $user->id)
                    ->value('account_id');

                if ($accountId) {
                    $model->setAccountId($accountId);
                }
            }
        });
    }

    /** @return BelongsTo<Account, $this> */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getAccountId(): string
    {
        return $this->account_id;
    }

    public function setAccountId(string $accountId): void
    {
        $this->account_id = $accountId;
    }
}
