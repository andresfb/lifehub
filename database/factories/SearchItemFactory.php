<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Date;
use App\Models\Account;
use App\Models\SearchItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SearchItem>
 */
class SearchItemFactory extends Factory
{
    protected $model = SearchItem::class;

    public function definition(): array
    {
        return [
            'entity_id' => $this->faker->word(),
            'entity_type' => $this->faker->word(),
            'module' => $this->faker->word(),
            'title' => $this->faker->word(),
            'body' => $this->faker->word(),
            'tags' => $this->faker->words(),
            'keyboards' => $this->faker->words(),
            'metadata' => $this->faker->words(),
            'url' => $this->faker->url(),
            'is_private' => $this->faker->boolean(),
            'is_archived' => $this->faker->boolean(),
            'source_updated_at' => Date::now(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
            'keywords' => $this->faker->words(),
            'urls' => $this->faker->words(),

            'account_id' => Account::factory(),
        ];
    }
}
