<?php

declare(strict_types=1);

namespace Modules\Core\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Models\AiModel;
use Modules\Core\Models\AiProvider;

/**
 * @extends Factory<AiModel>
 */
final class AiModelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ai_provider_id' => AiProvider::factory(),
            'user_id' => User::factory(),
            'name' => fake()->unique()->word(),
            'enabled' => true,
            'supports_text' => true,
            'supports_images' => false,
            'supports_tts' => false,
            'supports_stt' => false,
            'supports_embeddings' => false,
            'supports_reranking' => false,
            'supports_files' => false,
        ];
    }

    public function configure(): self
    {
        return $this->afterMaking(function (AiModel $model): void {
            if ($model->provider instanceof AiProvider && blank($model->user_id)) {
                $model->user_id = $model->provider->user_id;
            }
        })->afterCreating(function (AiModel $model): void {
            if ($model->user_id !== $model->provider->user_id) {
                $model->forceFill([
                    'user_id' => $model->provider->user_id,
                ])->save();
            }
        });
    }
}
