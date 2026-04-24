<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiManifest;
use App\Models\ApiManifestModule;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ApiManifestModuleFactory extends Factory
{
    protected $model = ApiManifestModule::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word(),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'is_public' => $this->faker->boolean(),
            'sort_order' => $this->faker->randomNumber(),

            'api_manifest_id' => ApiManifest::factory(),
        ];
    }
}
