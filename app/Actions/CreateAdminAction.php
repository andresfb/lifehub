<?php

namespace App\Actions;

use App\Dtos\NewUserItem;
use App\Enums\AccountType;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CreateAdminAction
{
    /**
     * @throws Throwable
     */
    public function handle(NewUserItem $input): string
    {
        return DB::transaction(function () use ($input): string {
            $user = User::query()->create([
                'name' => $input->name,
                'email' => $input->email,
                'password' => Hash::make($input->password),
            ]);

            $user->email_verified_at = now();
            $user->is_admin = base64_encode(
                hash_hmac('sha256', $user->id, Config::string('app.key'))
            );
            $user->save();

            $this->createAccount($user);

            return $user->createToken('auth-token')->plainTextToken;
        });
    }

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
