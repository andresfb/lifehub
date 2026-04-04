<?php

namespace Database\Factories;

use App\Models\ApiError;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ApiErrorFactory extends Factory
{
    protected $model = ApiError::class;

    public function definition(): array
    {
        return [
            'source_id' => $this->faker->word(),
            'type' => $this->faker->word(),
            'error' => $this->faker->word(),
            'data' => $this->faker->words(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
