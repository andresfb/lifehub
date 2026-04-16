<?php

declare(strict_types=1);

namespace App\Services\AI;

use App\Models\AiProvider;
use App\Models\User;
use App\Models\UserSetting;

final readonly class UserAiSettingsService
{
    public function __construct(
        private ProviderCatalog $catalog
    ) {}

    public function ensureRootSetting(User $user): UserSetting
    {
        return UserSetting::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'key' => UserSetting::AI_KEY,
            ],
            ['payload' => []],
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createProvider(User $user, array $attributes): AiProvider
    {
        $setting = $this->ensureRootSetting($user);
        $code = (string) $attributes['code'];

        return $setting->aiProviders()->create([
            'user_id' => $user->id,
            'code' => $code,
            'name' => (string) ($attributes['name'] ?? $this->catalog->label($code)),
            'enabled' => (bool) ($attributes['enabled'] ?? true),
            'api_key' => (string) $attributes['api_key'],
            'url' => ($attributes['url'] ?? null) ?: null,
            'api_version' => ($attributes['api_version'] ?? null) ?: null,
            'deployment' => ($attributes['deployment'] ?? null) ?: null,
            'embedding_deployment' => ($attributes['embedding_deployment'] ?? null) ?: null,
        ]);
    }
}
