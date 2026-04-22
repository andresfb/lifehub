<?php

namespace Database\Factories;

use App\Models\ApiCatalog;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiCatalogFactory extends Factory
{
    protected $model = ApiCatalog::class;

    public function definition(): array
    {
        return [
            'version' => $this->faker->word(),
            'raw_payload' => $this->faker->words(),
            'user_id' => $this->faker->randomNumber(),
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ];
    }
}
