<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Date;
use App\Models\Account;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reminder>
 */
class ReminderFactory extends Factory
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

            'account_id' => Account::factory(),
        ];
    }
}
