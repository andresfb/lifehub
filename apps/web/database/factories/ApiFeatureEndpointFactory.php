<?php

namespace Database\Factories;

use App\Models\ApiFeatureEndpoint;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiFeatureEndpointFactory extends Factory
{
    protected $model = ApiFeatureEndpoint::class;

    public function definition(): array
    {
        return [
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ];
    }
}
