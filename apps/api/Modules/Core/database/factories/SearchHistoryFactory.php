<?php

declare(strict_types=1);

namespace Modules\Core\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;
use Modules\Core\Models\SearchHistory;

/**
 * @extends Factory<SearchHistory>
 */
final class SearchHistoryFactory extends Factory
{
    protected $model = SearchHistory::class;

    public function definition(): array
    {
        return [
            'module' => $this->faker->word(),
            'type' => $this->faker->word(),
            'hash' => $this->faker->word(),
            'query' => $this->faker->word(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),

            'user_id' => User::factory(),
        ];
    }
}
