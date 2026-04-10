<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

use function Pest\Laravel\actingAs;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    resolve(ModuleAccessService::class)->syncPermissions();
});

test('guests cannot access the app manifest', function (): void {
    $this->getJson('/api/v1/app-manifest')->assertUnauthorized();
});

test('authenticated users can access the app manifest', function (): void {
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/app-manifest')
        ->assertSuccessful()
        ->assertJsonStructure(['version', 'modules']);
});

test('manifest only includes modules the user has access to', function (): void {
    $user = User::factory()->create();

    $response = actingAs($user, 'sanctum')
        ->getJson('/api/v1/app-manifest')
        ->assertSuccessful();

    expect($response->json('modules'))->toBeEmpty();
});

test('manifest includes the dashboard module for users with dashboard read access', function (): void {
    $user = User::factory()->create();
    resolve(ModuleAccessService::class)->grantReader($user, 'dashboard');

    $response = actingAs($user, 'sanctum')
        ->getJson('/api/v1/app-manifest')
        ->assertSuccessful();

    $modules = collect($response->json('modules'));

    expect($modules)->toHaveCount(1)
        ->and($modules->first()['key'])->toBe('dashboard')
        ->and($modules->first()['name'])->toBe('Dashboard')
        ->and($modules->first()['features'])->not->toBeEmpty();
});

test('manifest features include resolved endpoint bindings', function (): void {
    $user = User::factory()->create();
    resolve(ModuleAccessService::class)->grantReader($user, 'dashboard');

    $response = actingAs($user, 'sanctum')
        ->getJson('/api/v1/app-manifest')
        ->assertSuccessful();

    $modules = collect($response->json('modules'));
    $dashboardModule = $modules->firstWhere('key', 'dashboard');
    $homeFeature = collect($dashboardModule['features'])->firstWhere('id', 'dashboard.home');

    expect($homeFeature)->not->toBeNull()
        ->and($homeFeature['endpoints'])->not->toBeEmpty()
        ->and($homeFeature['endpoints'][0])->toHaveKeys(['route_name', 'method', 'path', 'operation_id'])
        ->and($homeFeature['endpoints'][0]['method'])->toBe('GET')
        ->and($homeFeature['endpoints'][0]['path'])->toContain('dashboard')
        ->and($homeFeature['endpoints'][0]['operation_id'])->toBe('v1.dashboard');
});

test('write-only features are hidden from read-only users', function (): void {
    $user = User::factory()->create();
    resolve(ModuleAccessService::class)->grantReader($user, 'dashboard');

    $response = actingAs($user, 'sanctum')
        ->getJson('/api/v1/app-manifest')
        ->assertSuccessful();

    $modules = collect($response->json('modules'));
    $dashboardModule = $modules->firstWhere('key', 'dashboard');
    $allFeatureIds = collectAllFeatureIds($dashboardModule['features']);

    expect($allFeatureIds)->not->toContain('dashboard.pins.manage')
        ->and($allFeatureIds)->not->toContain('dashboard.search.manage');
});

test('write features are included for users with write access', function (): void {
    $user = User::factory()->create();
    resolve(ModuleAccessService::class)->grantWriter($user, 'dashboard');

    $response = actingAs($user, 'sanctum')
        ->getJson('/api/v1/app-manifest')
        ->assertSuccessful();

    $modules = collect($response->json('modules'));
    $dashboardModule = $modules->firstWhere('key', 'dashboard');
    $allFeatureIds = collectAllFeatureIds($dashboardModule['features']);

    expect($allFeatureIds)->toContain('dashboard.pins.manage')
        ->and($allFeatureIds)->toContain('dashboard.search.manage');
});

test('manifest version comes from app config', function (): void {
    $user = User::factory()->create();
    config(['app.version' => '2.0.0']);

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/app-manifest')
        ->assertSuccessful()
        ->assertJsonPath('version', '2.0.0');
});

test('manifest cache is cleared by artisan command', function (): void {
    $this->artisan('manifest:clear')->assertSuccessful();
});

/**
 * Recursively collect all feature IDs from a feature tree.
 *
 * @param  array<int, array<string, mixed>>  $features
 * @return list<string>
 */
function collectAllFeatureIds(array $features): array
{
    $ids = [];
    foreach ($features as $feature) {
        $ids[] = $feature['id'];
        if (! empty($feature['children'])) {
            $ids = array_merge($ids, collectAllFeatureIds($feature['children']));
        }
    }

    return $ids;
}
