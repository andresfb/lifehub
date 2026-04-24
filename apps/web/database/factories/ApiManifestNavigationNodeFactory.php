<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApiManifestModule;
use App\Models\ApiManifestNavigationNode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiManifestNavigationNode>
 */
final class ApiManifestNavigationNodeFactory extends Factory
{
    protected $model = ApiManifestNavigationNode::class;

    public function definition(): array
    {
        return [
            'node_id' => $this->faker->word(),
            'key' => $this->faker->word(),
            'name' => $this->faker->name(),
            'web_path' => $this->faker->word(),
            'icon' => $this->faker->word(),
            'shortcut' => $this->faker->word(),
            'show' => $this->faker->boolean(),
            'sort_order' => $this->faker->randomNumber(),

            'api_manifest_module_id' => ApiManifestModule::factory(),
            'parent_id' => ApiManifestNavigationNode::factory(),
        ];
    }
}
