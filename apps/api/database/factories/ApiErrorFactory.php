<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiError;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<ApiError>
 */
final class ApiErrorFactory extends Factory
{
    protected $model = ApiError::class;

    public function definition(): array
    {
        return [
            'source_id' => $this->faker->word(),
            'type' => $this->faker->word(),
            'error' => $this->faker->word(),
            'data' => $this->faker->words(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
