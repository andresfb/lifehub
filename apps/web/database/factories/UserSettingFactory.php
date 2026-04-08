<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    public function definition(): array
    {
        return [
            'group' => $this->faker->word(),
            'code' => $this->faker->word(),
            'key' => $this->faker->word(),
            'value' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
