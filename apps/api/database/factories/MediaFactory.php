<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Media>
 */
final class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'collection_name' => $this->faker->name(),
            'name' => $this->faker->name(),
            'file_name' => $this->faker->name(),
            'mime_type' => $this->faker->word(),
            'disk' => $this->faker->word(),
            'conversions_disk' => $this->faker->word(),
            'size' => $this->faker->randomNumber(),
            'manipulations' => $this->faker->words(),
            'custom_properties' => $this->faker->words(),
            'generated_conversions' => $this->faker->words(),
            'responsive_images' => $this->faker->words(),
            'order_column' => $this->faker->randomNumber(),
            'is_encrypted' => $this->faker->boolean(),
            'encryption_key_version' => $this->faker->word(),
            'encryption_alg' => $this->faker->word(),
            'encryption_metadata' => $this->faker->words(),
            'model_id' => $this->faker->word(),
            'model_type' => $this->faker->word(),
            'uuid' => $this->faker->uuid(),
            'created_at' => Date::now(),
            'updated_at' => Date::now(),
            'extension' => $this->faker->word(),
            'type' => $this->faker->word(),
        ];
    }
}
