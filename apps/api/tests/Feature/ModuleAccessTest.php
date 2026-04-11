<?php

declare(strict_types=1);

use App\Actions\CreateAdminAction;
use App\Dtos\Profile\NewUserItem;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Nwidart\Modules\Facades\Module as ModuleFacade;
use Nwidart\Modules\Laravel\Module as LaravelModule;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    resolve(ModuleAccessService::class)->syncPermissions();
});

test('guests cannot access core module routes', function (): void {
    $this->getJson('/api/v1/cores')->assertUnauthorized();
});

test('users without a core role cannot access core module routes', function (): void {
    $user = User::factory()->create();

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/cores')
        ->assertForbidden();
});

test('core readers can read core routes', function (): void {
    $user = User::factory()->create();

    resolve(ModuleAccessService::class)->grantReader($user, 'core');

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/cores')
        ->assertSuccessful();

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/cores/1')
        ->assertSuccessful();
});

test('core readers cannot write core routes', function (string $method, string $uri): void {
    $user = User::factory()->create();

    resolve(ModuleAccessService::class)->grantReader($user, 'core');

    actingAs($user, 'sanctum')
        ->json($method, $uri)
        ->assertForbidden();
})->with([
    ['POST', '/api/v1/cores'],
    ['PUT', '/api/v1/cores/1'],
    ['PATCH', '/api/v1/cores/1'],
    ['DELETE', '/api/v1/cores/1'],
]);

test('core writers can read and write core routes', function (string $method, string $uri): void {
    $user = User::factory()->create();

    resolve(ModuleAccessService::class)->grantWriter($user, 'core');

    actingAs($user, 'sanctum')
        ->json($method, $uri)
        ->assertSuccessful();
})->with([
    ['GET', '/api/v1/cores'],
    ['GET', '/api/v1/cores/1'],
    ['POST', '/api/v1/cores'],
    ['PUT', '/api/v1/cores/1'],
    ['PATCH', '/api/v1/cores/1'],
    ['DELETE', '/api/v1/cores/1'],
]);

test('super admins can access every core route', function (string $method, string $uri): void {
    $user = User::factory()->create();

    resolve(ModuleAccessService::class)->grantSuperAdmin($user);

    actingAs($user, 'sanctum')
        ->json($method, $uri)
        ->assertSuccessful();
})->with([
    ['GET', '/api/v1/cores'],
    ['GET', '/api/v1/cores/1'],
    ['POST', '/api/v1/cores'],
    ['PUT', '/api/v1/cores/1'],
    ['PATCH', '/api/v1/cores/1'],
    ['DELETE', '/api/v1/cores/1'],
]);

test('public modules grant default writer access and private modules do not', function (): void {
    ModuleFacade::shouldReceive('allEnabled')->andReturn([
        moduleFixture('PublicModule'),
        moduleFixture('PrivateModule'),
    ]);

    $user = User::factory()->create();
    $moduleAccess = resolve(ModuleAccessService::class);

    $moduleAccess->grantPublicWriters($user);

    expect($moduleAccess->enabledModuleKeys()->all())->toBe(['public-module', 'private-module'])
        ->and($moduleAccess->publicModuleKeys()->all())->toBe(['public-module'])
        ->and($moduleAccess->canWrite($user, 'public-module'))->toBeTrue()
        ->and($moduleAccess->canRead($user, 'public-module'))->toBeTrue()
        ->and($moduleAccess->canWrite($user, 'private-module'))->toBeFalse()
        ->and($moduleAccess->canRead($user, 'private-module'))->toBeFalse();
});

test('super admins receive writer roles for all enabled modules', function (): void {
    ModuleFacade::shouldReceive('allEnabled')->andReturn([
        moduleFixture('PublicModule'),
        moduleFixture('PrivateModule'),
    ]);

    $user = User::factory()->create();
    $moduleAccess = resolve(ModuleAccessService::class);

    $moduleAccess->grantSuperAdmin($user);

    expect($user->hasRole(ModuleAccessService::SUPER_ADMIN_ROLE))->toBeTrue()
        ->and($moduleAccess->canWrite($user, 'public-module'))->toBeTrue()
        ->and($moduleAccess->canWrite($user, 'private-module'))->toBeTrue();
});

test('sync modules command grants all enabled module writers to existing super admins', function (): void {
    ModuleFacade::shouldReceive('allEnabled')->andReturn([
        moduleFixture('PublicModule'),
        moduleFixture('PrivateModule'),
    ]);

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(ModuleAccessService::SUPER_ADMIN_ROLE);

    $this->artisan('sync:modules')->assertSuccessful();

    $moduleAccess = resolve(ModuleAccessService::class);

    expect($superAdmin->fresh()->hasRole($moduleAccess->writerRoleName('public-module')))->toBeTrue()
        ->and($superAdmin->fresh()->hasRole($moduleAccess->writerRoleName('private-module')))->toBeTrue();
});

test('create admin action assigns the super admin role', function (): void {
    $user = resolve(CreateAdminAction::class)->handle(new NewUserItem(
        name: 'Admin User',
        email: 'admin@example.com',
        password: 'password',
    ));

    expect($user->isAdmin())->toBeTrue()
        ->and($user->hasRole(ModuleAccessService::SUPER_ADMIN_ROLE))->toBeTrue();
});

test('module permission sync is idempotent', function (): void {
    $moduleAccess = resolve(ModuleAccessService::class);

    $moduleAccess->syncPermissions();
    $moduleAccess->syncPermissions();

    $writerRole = Role::findByName($moduleAccess->writerRoleName('core'));

    expect($moduleAccess->enabledModuleKeys()->all())->toContain('core')
        ->and($moduleAccess->readerRoleName('core'))->toBe('module.core.reader')
        ->and($moduleAccess->writerRoleName('core'))->toBe('module.core.writer')
        ->and($moduleAccess->permissionName('core', 'read'))->toBe('module.core.read')
        ->and($moduleAccess->permissionName('core', 'write'))->toBe('module.core.write')
        ->and($writerRole->hasPermissionTo($moduleAccess->permissionName('core', 'read')))->toBeTrue()
        ->and($writerRole->hasPermissionTo($moduleAccess->permissionName('core', 'write')))->toBeTrue();
});

function moduleFixture(string $name): LaravelModule
{
    return new LaravelModule(app(), $name, base_path("tests/Fixtures/Modules/{$name}"));
}
