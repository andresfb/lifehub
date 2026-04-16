<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSetting>
 */
final class UserSettingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'key' => fake()->unique()->word(),
            'payload' => [],
        ];
    }

    public function ai(): self
    {
        return $this->state(fn (): array => [
            'key' => UserSetting::AI_KEY,
            'payload' => [],
        ]);
    }
}
