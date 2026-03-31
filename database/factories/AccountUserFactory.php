<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\AccountUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<AccountUser>
 */
final class AccountUserFactory extends Factory
{
    protected $model = AccountUser::class;

    public function definition(): array
    {
        return [
            'role' => $this->faker->word(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'account_id' => Account::factory(),
            'user_id' => User::factory(),
        ];
    }
}
