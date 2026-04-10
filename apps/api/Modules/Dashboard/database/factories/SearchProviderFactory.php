<?php

declare(strict_types=1);

namespace Modules\Dashboard\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;
use Modules\Dashboard\Models\SearchProvider;

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
