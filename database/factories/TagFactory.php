<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Support\Facades\Date;
use App\Models\Account;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
final class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'slug' => $this->faker->slug(),
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'order_column' => $this->faker->randomNumber(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];
    }
}
