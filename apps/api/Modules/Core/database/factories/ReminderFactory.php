<?php

declare(strict_types=1);

namespace Modules\Core\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;
use Modules\Core\Models\Reminder;

/**
 * @extends Factory<Reminder>
 */
final class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'due_at' => Date::now(),
            'completed_at' => Date::now(),
            'snoozed_until' => Date::now(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
