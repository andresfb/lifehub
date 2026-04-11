<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Dashboard\Models\SearchProvider;

uses(RefreshDatabase::class);

test('setting a provider as default unsets the previous default for the same user', function (): void {
    $user = User::factory()->create();

    $first = SearchProvider::factory()->for($user)->default()->create();
    $second = SearchProvider::factory()->for($user)->create();

    $second->default = true;
    $second->save();

    expect($second->fresh()->default)->toBeTrue()
        ->and($first->fresh()->default)->toBeFalse();
});

test('setting a provider as default does not affect other users defaults', function (): void {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    SearchProvider::factory()->for($userA)->default()->create();
    $providerB = SearchProvider::factory()->for($userB)->default()->create();

    $new = SearchProvider::factory()->for($userA)->create();
    $new->default = true;
    $new->save();

    expect($providerB->fresh()->default)->toBeTrue();
});

test('saving a non-default provider does not unset other defaults', function (): void {
    $user = User::factory()->create();

    $default = SearchProvider::factory()->for($user)->default()->create();
    $other = SearchProvider::factory()->for($user)->create();

    $other->name = 'Updated name';
    $other->save();

    expect($default->fresh()->default)->toBeTrue();
});

test('cache is flushed for the user after saving a provider', function (): void {
    Cache::spy();

    $user = User::factory()->create();
    $provider = SearchProvider::factory()->for($user)->create();

    $provider->name = 'New name';
    $provider->save();

    Cache::shouldHaveReceived('tags')
        ->atLeast()->once()
        ->with("SearchProviders:{$user->id}");
});
