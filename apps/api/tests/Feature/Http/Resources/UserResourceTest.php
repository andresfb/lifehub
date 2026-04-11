<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiRequest;

uses(LazilyRefreshDatabase::class);

test('user resource returns a json api response', function (): void {
    $user = User::factory()->create([
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.com',
        'email_verified_at' => now(),
        'two_factor_confirmed_at' => now(),
    ]);

    $response = (new UserResource($user))
        ->toResponse(Request::create('/api/v1/me', 'GET'));

    $payload = $response->getData(true);

    expect($response->headers->get('Content-Type'))->toBe('application/vnd.api+json')
        ->and($payload['data']['id'])->toBe((string) $user->id)
        ->and($payload['data']['type'])->toBe('user')
        ->and($payload['data']['attributes'])->toMatchArray([
            'name' => 'Taylor Otwell',
            'email' => 'taylor@example.com',
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'two_factor_enabled' => true,
            'created_at' => $user->created_at?->toIso8601String(),
            'updated_at' => $user->updated_at?->toIso8601String(),
        ]);
});

test('user resource can be embedded without an extra data wrapper', function (): void {
    $user = User::factory()->create([
        'name' => 'Taylor Otwell',
        'email' => 'taylor@example.com',
    ]);

    $payload = (new UserResource($user))
        ->resolveResourceData(JsonApiRequest::createFromBase(
            Request::create('/api/v1/login', 'POST')
        ));

    expect($payload['id'])->toBe((string) $user->id)
        ->and($payload['type'])->toBe('user')
        ->and($payload['attributes'])->toBeObject()
        ->and($payload['attributes']->name)->toBe('Taylor Otwell')
        ->and($payload['attributes']->email)->toBe('taylor@example.com')
        ->and($payload)->not->toHaveKey('data');
});
