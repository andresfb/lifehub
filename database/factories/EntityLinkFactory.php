<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EntityLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<EntityLink>
 */
final class EntityLinkFactory extends Factory
{
    protected $model = EntityLink::class;

    public function definition(): array
    {
        return [
            'source_id' => $this->faker->randomNumber(),
            'source_type' => $this->faker->word(),
            'target_id' => $this->faker->randomNumber(),
            'target_type' => $this->faker->word(),
            'relation_type' => $this->faker->word(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
