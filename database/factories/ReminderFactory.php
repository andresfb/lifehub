<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'notes' => $this->faker->word(),
            'due_at' => Carbon::now(),
            'completed_at' => Carbon::now(),
            'snoozed_until' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'account_id' => Account::factory(),
        ];
    }
}
