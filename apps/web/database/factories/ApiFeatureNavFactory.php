<?php

namespace Database\Factories;

use App\Models\ApiFeatureNav;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiFeatureNavFactory extends Factory
{
    protected $model = ApiFeatureNav::class;

    public function definition(): array
    {
        return [
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ];
    }
}
