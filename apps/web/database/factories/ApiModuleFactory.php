<?php

namespace Database\Factories;

use App\Models\ApiCatalog;
use App\Models\ApiModule;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiModuleFactory extends Factory
{
    protected $model = ApiModule::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word(),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'is_public' => $this->faker->boolean(),
            'sort_order' => $this->faker->randomNumber(),
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),

            'api_catalog_id' => ApiCatalog::factory(),
        ];
    }
}
