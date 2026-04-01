<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Date;
use App\Models\Audit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audit>
 */
class AuditFactory extends Factory
{
    protected $model = Audit::class;

    public function definition(): array
    {
        return [
            'auditable_type' => $this->faker->word(),
            'tags' => $this->faker->word(),
            'user' => $this->faker->word(),
            'old_values' => $this->faker->word(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
            'auditable' => $this->faker->word(),
            'auditable_id' => $this->faker->randomNumber(),
            'new_values' => $this->faker->word(),
            'event' => $this->faker->word(),
        ];
    }
}
