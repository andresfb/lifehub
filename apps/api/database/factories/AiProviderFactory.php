<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiProvider;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiProvider>
 */
final class AiProviderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_setting_id' => UserSetting::factory()->ai(),
            'user_id' => User::factory(),
            'code' => 'openai',
            'name' => 'OpenAI',
            'enabled' => true,
            'api_key' => 'sk-test-key',
            'url' => 'https://api.openai.com/v1',
            'api_version' => null,
            'deployment' => null,
            'embedding_deployment' => null,
        ];
    }

    public function configure(): self
    {
        return $this->afterMaking(function (AiProvider $provider): void {
            if (blank($provider->user_id) && $provider->userSetting instanceof UserSetting) {
                $provider->user_id = $provider->userSetting->user_id;
            }
        })->afterCreating(function (AiProvider $provider): void {
            if ($provider->user_id !== $provider->userSetting->user_id) {
                $provider->forceFill([
                    'user_id' => $provider->userSetting->user_id,
                ])->save();
            }
        });
    }

    public function azure(): self
    {
        return $this->state(fn (): array => [
            'code' => 'azure',
            'name' => 'Azure',
            'url' => 'https://example.openai.azure.com',
            'api_version' => '2024-10-21',
            'deployment' => 'gpt-4o',
            'embedding_deployment' => 'text-embedding-3-small',
        ]);
    }
}
