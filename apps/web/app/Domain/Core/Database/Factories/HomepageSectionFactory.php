<?php

declare(strict_types=1);

namespace App\Domain\Core\Database\Factories;

use App\Domain\Core\Models\HomepageSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<HomepageSection>
 */
final class HomepageSectionFactory extends Factory
{
    protected $model = HomepageSection::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->slug(),
            'name' => $this->faker->name(),
            'active' => $this->faker->boolean(),
            'order' => $this->faker->randomDigit(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
