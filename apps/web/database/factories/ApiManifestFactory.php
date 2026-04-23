<?php

namespace Database\Factories;

use App\Models\ApiManifest;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiManifestFactory extends Factory
{
    protected $model = ApiManifest::class;

    public function definition(): array
    {
        return [
            'version' => $this->faker->word(),
            'payload' => $this->faker->words(),
            'user_id' => $this->faker->randomNumber(),
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ];
    }
}
