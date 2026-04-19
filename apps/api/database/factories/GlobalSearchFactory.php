<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GlobalSearch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<GlobalSearch>
 */
final class GlobalSearchFactory extends Factory
{
    protected $model = GlobalSearch::class;

    public function definition(): array
    {
        return [
            'entity_type' => $this->faker->word(),
            'entity_id' => $this->faker->randomNumber(),
            'creator_id' => $this->faker->unique()->uuid(),
            'module' => $this->faker->word(),
            'title' => $this->faker->word(),
            'body' => $this->faker->word(),
            'tags' => $this->faker->words(),
            'keywords' => $this->faker->words(),
            'metadata' => $this->faker->words(),
            'urls' => $this->faker->words(),
            'is_private' => $this->faker->boolean(),
            'is_archived' => $this->faker->boolean(),
            'source_updated_at' => Date::now(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
