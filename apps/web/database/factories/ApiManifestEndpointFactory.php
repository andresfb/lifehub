<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiManifest;
use App\Models\ApiManifestEndpoint;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ApiManifestEndpointFactory extends Factory
{
    protected $model = ApiManifestEndpoint::class;

    public function definition(): array
    {
        return [
            'route_name' => $this->faker->name(),
            'method' => $this->faker->word(),
            'path' => $this->faker->word(),
            'operation_id' => $this->faker->word(),

            'api_manifest_id' => ApiManifest::factory(),
        ];
    }
}
