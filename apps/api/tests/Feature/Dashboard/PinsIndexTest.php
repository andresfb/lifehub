<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Dashboard\Models\HomepageItem;
use Modules\Dashboard\Models\HomepageSection;

use function Pest\Laravel\actingAs;

uses(LazilyRefreshDatabase::class);

beforeEach(function (): void {
    resolve(ModuleAccessService::class)->syncPermissions();
});

test('pins index filters items by status', function (?int $status, array $expectedTitles, array $unexpectedTitles): void {
    $user = User::factory()->create();

    resolve(ModuleAccessService::class)->grantReader($user, 'dashboard');

    $section = HomepageSection::factory()->for($user)->create([
        'active' => true,
        'order' => 1,
    ]);

    HomepageSection::factory()->for($user)->create([
        'name' => 'Empty Section',
        'active' => true,
        'order' => 2,
    ]);

    HomepageItem::factory()->for($user)->for($section, 'section')->create([
        'slug' => 'active-item',
        'title' => 'Active Item',
        'url' => 'https://example.com/active',
        'active' => true,
        'order' => 1,
    ]);

    HomepageItem::factory()->for($user)->for($section, 'section')->create([
        'slug' => 'inactive-item',
        'title' => 'Inactive Item',
        'url' => 'https://example.com/inactive',
        'active' => false,
        'order' => 2,
    ]);

    $query = $status === null ? [] : ['status' => $status];

    $response = actingAs($user, 'sanctum')
        ->getJson('/api/v1/dashboard/pins?'.http_build_query($query))
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $titles = collect($response->json('data.0.items'))
        ->pluck('title')
        ->sort()
        ->values()
        ->all();

    expect($titles)->toBe(collect($expectedTitles)->sort()->values()->all());

    foreach ($unexpectedTitles as $title) {
        expect($titles)->not->toContain($title);
    }
})->with([
    'defaults to active items' => [null, ['Active Item'], ['Inactive Item']],
    'returns active items' => [1, ['Active Item'], ['Inactive Item']],
    'returns inactive items' => [0, ['Inactive Item'], ['Active Item']],
    'returns all items' => [-1, ['Active Item', 'Inactive Item'], []],
]);

test('pins index validates status', function (): void {
    $user = User::factory()->create();

    resolve(ModuleAccessService::class)->grantReader($user, 'dashboard');

    actingAs($user, 'sanctum')
        ->getJson('/api/v1/dashboard/pins?status=2')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('status');
});
