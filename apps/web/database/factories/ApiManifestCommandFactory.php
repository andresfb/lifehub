<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiManifestCommand;
use App\Models\ApiManifestEndpoint;
use App\Models\ApiManifestModule;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ApiManifestCommandFactory extends Factory
{
    protected $model = ApiManifestCommand::class;

    public function definition(): array
    {
        return [
            'owner' => $this->faker->word(),
            'code' => $this->faker->word(),
            'name' => $this->faker->name(),
            'required_access' => $this->faker->word(),
            'shortcut' => $this->faker->word(),
            'sort_order' => $this->faker->randomNumber(),

            'api_manifest_module_id' => ApiManifestModule::factory(),
            'api_manifest_endpoint_id' => ApiManifestEndpoint::factory(),
        ];
    }
}
