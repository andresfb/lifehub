<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Database\Factories;

use App\Domain\Dashboard\Models\SearchProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<SearchProvider>
 */
final class SearchProviderFactory extends Factory
{
    protected $model = SearchProvider::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'url' => $this->faker->url(),
            'active' => $this->faker->boolean(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
