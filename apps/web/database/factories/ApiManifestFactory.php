<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiManifest;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiManifest>
 */
final class ApiManifestFactory extends Factory
{
    protected $model = ApiManifest::class;

    public function definition(): array
    {
        return [
            'version' => $this->faker->word(),
            'payload' => [
                'success' => true,
                'message' => 'Success',
                'data' => [
                    'version' => $this->faker->word(),
                    'modules' => [],
                ],
            ],
            'user_id' => $this->faker->randomNumber(),
            'created_at' => CarbonImmutable::now(),
            'updated_at' => CarbonImmutable::now(),
        ];
    }
}
