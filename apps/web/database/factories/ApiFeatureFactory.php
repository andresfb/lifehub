<?php

namespace Database\Factories;

use App\Models\ApiFeature;
use App\Models\ApiModule;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiFeatureFactory extends Factory
{
    protected $model = ApiFeature::class;

    public function definition(): array
    {
        return [
            'parent_id' => $this->faker->randomNumber(),
            'external_id' => $this->faker->word(),
            'title' => $this->faker->word(),
            'kind' => $this->faker->word(),
            'required_access' => $this->faker->word(),
            'sort_order' => $this->faker->word(),
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),

            'api_module_id' => ApiModule::factory(),
        ];
    }
}
