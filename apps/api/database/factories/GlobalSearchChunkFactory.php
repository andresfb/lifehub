<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GlobalSearch;
use App\Models\GlobalSearchChunk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GlobalSearchChunk>
 */
final class GlobalSearchChunkFactory extends Factory
{
    protected $model = GlobalSearchChunk::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $content = fake()->paragraph();

        return [
            'global_search_id' => GlobalSearch::factory(),
            'user_id' => User::factory(),
            'chunk_index' => fake()->numberBetween(0, 20),
            'content' => $content,
            'content_hash' => hash('sha256', $content),
            'content_length' => mb_strlen($content),
            'embedded_provider_code' => null,
            'embedded_model' => null,
            'embedded_dimensions' => null,
            'embedded_content_hash' => null,
            'embedded_at' => null,
            'embedding_failed_reason' => null,
            'embedding_failed_at' => null,
        ];
    }

    public function configure(): self
    {
        return $this->afterMaking(function (GlobalSearchChunk $chunk): void {
            if ($chunk->globalSearch instanceof GlobalSearch) {
                $chunk->user_id = $chunk->globalSearch->user_id;
            }
        })->afterCreating(function (GlobalSearchChunk $chunk): void {
            if ($chunk->user_id !== $chunk->globalSearch->user_id) {
                $chunk->forceFill([
                    'user_id' => $chunk->globalSearch->user_id,
                ])->save();
            }
        });
    }
}
