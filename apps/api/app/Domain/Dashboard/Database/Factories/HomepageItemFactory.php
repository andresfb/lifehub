<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Database\Factories;

use App\Domain\Dashboard\Models\HomepageItem;
use App\Domain\Dashboard\Models\HomepageSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<HomepageItem>
 */
final class HomepageItemFactory extends Factory
{
    protected $model = HomepageItem::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug(),
            'title' => $this->faker->word(),
            'url' => $this->faker->url(),
            'active' => $this->faker->boolean(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
            'section_id' => HomepageSection::factory(),
        ];
    }
}
