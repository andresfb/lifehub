<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\Models\AiModel;
use Modules\Core\Models\AiProvider;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

function idempotentHeaders(string $key): array
{
    return ['Idempotency-Key' => $key];
}

test('it lists the authenticated users ai providers and ensures the root settings row exists', function (): void {
    $user = User::factory()->create();
    $setting = UserSetting::factory()->ai()->for($user)->create();
    $provider = AiProvider::factory()->for($setting)->for($user)->create();
    AiModel::factory()->for($provider, 'provider')->for($user)->create([
        'name' => 'gpt-4.1-mini',
    ]);

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/me/ai/providers')
        ->assertOk()
        ->assertJsonPath('data.0.attributes.code', $provider->code)
        ->assertJsonPath('data.0.attributes.models.0.attributes.name', 'gpt-4.1-mini');

    expect($user->fresh()->aiSettings)->not->toBeNull();
});

test('it creates an ai provider for the authenticated user', function (): void {
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->withHeaders(idempotentHeaders('create-provider'))
        ->postJson('/api/v1/me/ai/providers', [
            'code' => 'openai',
            'api_key' => 'sk-user-openai',
            'url' => 'https://api.openai.com/v1',
        ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.code', 'openai')
        ->assertJsonPath('data.attributes.has_api_key', true);

    $provider = AiProvider::query()->where('user_id', $user->id)->firstOrFail();

    expect($provider->userSetting->key)->toBe(UserSetting::AI_KEY)
        ->and($provider->api_key)->toBe('sk-user-openai');
});

test('it validates driver specific fields when creating an ai provider', function (): void {
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->withHeaders(idempotentHeaders('invalid-provider'))
        ->postJson('/api/v1/me/ai/providers', [
            'code' => 'openai',
            'api_key' => 'sk-user-openai',
            'deployment' => 'not-supported',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['deployment']);
});

test('it creates an ai model with provider defaults and allows overrides', function (): void {
    $user = User::factory()->create();
    $setting = UserSetting::factory()->ai()->for($user)->create();
    $provider = AiProvider::factory()->for($setting)->for($user)->create([
        'code' => 'openai',
        'name' => 'OpenAI',
    ]);

    actingAs($user, 'sanctum')
        ->withHeaders(idempotentHeaders('create-model'))
        ->postJson("/api/v1/me/ai/providers/{$provider->id}/models", [
            'name' => 'gpt-4.1',
        ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', 'gpt-4.1')
        ->assertJsonPath('data.attributes.supports_text', true);
});

test('it prevents users from accessing another users ai settings', function (): void {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $setting = UserSetting::factory()->ai()->for($owner)->create();
    $provider = AiProvider::factory()->for($setting)->for($owner)->create();
    $model = AiModel::factory()->for($provider, 'provider')->for($owner)->create();

    actingAs($intruder, 'sanctum')
        ->getJson("/api/v1/me/ai/providers/{$provider->id}")
        ->assertNotFound();

    actingAs($intruder, 'sanctum')
        ->patchJson("/api/v1/me/ai/models/{$model->id}")
        ->assertNotFound();
});

test('it updates and deletes user ai records', function (): void {
    $user = User::factory()->create();
    $setting = UserSetting::factory()->ai()->for($user)->create();
    $provider = AiProvider::factory()->for($setting)->for($user)->create();
    $model = AiModel::factory()->for($provider, 'provider')->for($user)->create();

    actingAs($user, 'sanctum')
        ->withHeaders(idempotentHeaders('update-provider'))
        ->patchJson("/api/v1/me/ai/providers/{$provider->id}", [
            'name' => 'Primary OpenAI',
            'enabled' => false,
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Primary OpenAI')
        ->assertJsonPath('data.attributes.enabled', false);

    actingAs($user, 'sanctum')
        ->withHeaders(idempotentHeaders('update-model'))
        ->patchJson("/api/v1/me/ai/models/{$model->id}", [
            'supports_images' => true,
        ])
        ->assertOk()
        ->assertJsonPath('data.attributes.supports_images', true);

    actingAs($user, 'sanctum')
        ->withHeaders(idempotentHeaders('delete-model'))
        ->deleteJson("/api/v1/me/ai/models/{$model->id}")
        ->assertNoContent();

    actingAs($user, 'sanctum')
        ->withHeaders(idempotentHeaders('delete-provider'))
        ->deleteJson("/api/v1/me/ai/providers/{$provider->id}")
        ->assertNoContent();
});
