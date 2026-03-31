<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\AccountType;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\User;

trait AccountCreatable
{
    private function createAccount(User $user): void
    {
        $account = Account::query()
            ->updateOrCreate([
                'owner_user_id' => $user->id,
            ], [
                'name' => "$user->name Account",
            ]);

        AccountUser::query()
            ->updateOrCreate([
                'account_id' => $account->id,
                'user_id' => $user->id,
            ], [
                'role' => AccountType::OWNER,
            ]);
    }
}
