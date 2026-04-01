<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

final class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'slug' => $this->faker->slug(),
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'order_column' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
