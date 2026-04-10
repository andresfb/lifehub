<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Module>
 */
final class ModulesFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word(),
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'is_core' => $this->faker->boolean(),
            'is_public' => $this->faker->boolean(),
            'status' => $this->faker->word(),
            'settings_schema' => $this->faker->words(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
        ];
    }
}
