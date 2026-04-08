<?php

declare(strict_types=1);

namespace App\Domain\Core\Database\Factories;

use App\Domain\Core\Models\HomepageItem;
use App\Domain\Core\Models\HomepageSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'homepage_section_id' => HomepageSection::factory(),
        ];
    }
}
