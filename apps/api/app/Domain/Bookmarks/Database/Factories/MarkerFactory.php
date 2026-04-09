<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Database\Factories;

use App\Domain\Bookmarks\Models\Category;
use App\Domain\Bookmarks\Models\Marker;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Marker>
 */
final class MarkerFactory extends Factory
{
    protected $model = Marker::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->word(),
            'url' => $this->faker->url(),
            'domain' => $this->faker->word(),
            'title' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'site_title' => $this->faker->word(),
            'text' => $this->faker->text(),
            'priority' => $this->faker->randomNumber(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
