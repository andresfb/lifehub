<?php

namespace Database\Factories;

use App\Models\EntityLink;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EntityLinkFactory extends Factory
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
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
