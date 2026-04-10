<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Database\Factories;

use App\Domain\Bookmarks\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug(),
            'title' => $this->faker->unique()->title(),
            'order' => $this->faker->randomDigit(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),

            'user_id' => User::factory(),
        ];
    }
}
