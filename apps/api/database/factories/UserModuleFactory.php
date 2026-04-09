<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Module;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<UserModule>
 */
final class UserModuleFactory extends Factory
{
    protected $model = UserModule::class;

    public function definition(): array
    {
        return [
            'enabled' => $this->faker->boolean(),
            'access_level' => $this->faker->word(),
            'visibility' => $this->faker->word(),
            'settings' => $this->faker->words(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'module_id' => Module::factory(),
            'user_id' => User::factory(),
            'granted_by' => User::factory(),
        ];
    }
}
