<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Tag>
 */
final class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => $this->faker->slug(),
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'order_column' => $this->faker->randomNumber(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];
    }
}
