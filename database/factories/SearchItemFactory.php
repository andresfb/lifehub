<?php

namespace Database\Factories;

use App\Models\SearchItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SearchItemFactory extends Factory
{
    protected $model = SearchItem::class;

    public function definition(): array
    {
        return [
            'entity_type' => $this->faker->word(),
            'entity_id' => $this->faker->randomNumber(),
            'module' => $this->faker->word(),
            'title' => $this->faker->word(),
            'body' => $this->faker->word(),
            'tags' => $this->faker->words(),
            'keywords' => $this->faker->words(),
            'metadata' => $this->faker->words(),
            'urls' => $this->faker->words(),
            'is_private' => $this->faker->boolean(),
            'is_archived' => $this->faker->boolean(),
            'keyboards' => $this->faker->words(),
            'source_updated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
