<?php

declare(strict_types=1);

use App\Exceptions\UserAiConfigurationException;
use App\Models\AiModel;
use App\Models\AiProvider;
use App\Models\User;
use App\Models\UserSetting;
use App\Services\AI\UserAiResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Ai;

uses(RefreshDatabase::class);

test('it resolves a configured user ai provider into runtime config', function (): void {
    $user = User::factory()->create();
    $setting = UserSetting::factory()->ai()->for($user)->create();
    $provider = AiProvider::factory()->for($setting)->for($user)->create([
        'code' => 'openai',
        'api_key' => 'sk-runtime-openai',
        'url' => 'https://api.openai.com/v1',
    ]);
    AiModel::factory()->for($provider, 'provider')->for($user)->create([
        'name' => 'gpt-4.1-mini',
        'supports_text' => true,
    ]);

    Ai::purge();

    $resolved = resolve(UserAiResolver::class)->resolve($user, 'text');

    expect($resolved->code)->toBe('openai')
        ->and($resolved->model)->toBe('gpt-4.1-mini')
        ->and(config("ai.providers.{$resolved->name}.key"))->toBe('sk-runtime-openai')
        ->and(config("ai.providers.{$resolved->name}.url"))->toBe('https://api.openai.com/v1');
});

test('it throws when the user has no configured model for a feature', function (): void {
    $user = User::factory()->create();
    $setting = UserSetting::factory()->ai()->for($user)->create();
    $provider = AiProvider::factory()->for($setting)->for($user)->create();
    AiModel::factory()->for($provider, 'provider')->for($user)->create([
        'supports_text' => false,
    ]);

    expect(fn () => resolve(UserAiResolver::class)->resolve($user, 'text'))
        ->toThrow(UserAiConfigurationException::class);
});
