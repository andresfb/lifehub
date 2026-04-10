<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<\App\Models\Setting>
 */
final class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'group' => $this->faker->word(),
            'name' => $this->faker->name(),
            'locked' => $this->faker->boolean(),
            'payload' => $this->faker->words(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
