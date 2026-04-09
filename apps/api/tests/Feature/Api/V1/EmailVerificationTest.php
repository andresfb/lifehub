<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

describe('Email Verification', function (): void {
    it('verifies email with valid signed link', function (): void {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $path = parse_url($verificationUrl, PHP_URL_PATH).'?'.parse_url($verificationUrl, PHP_URL_QUERY);

        $response = $this->actingAs($user)->getJson($path);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Email verified successfully',
            ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    });

    it('fails verification without authentication', function (): void {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $path = parse_url($verificationUrl, PHP_URL_PATH).'?'.parse_url($verificationUrl, PHP_URL_QUERY);

        $response = $this->getJson($path);

        $response->assertStatus(401);
    });

    it('fails verification with invalid signature', function (): void {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
            ->getJson(sprintf('/email/verify/%d/invalid-hash', $user->id));

        $response->assertStatus(403);
    });
});

describe('Resend Verification Email', function (): void {
    it('resends verification email for unverified user', function (): void {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)
            ->postJson('/email/verification-notification');

        $response->assertStatus(202);
    });

    it('requires authentication', function (): void {
        $response = $this->postJson('/email/verification-notification');

        $response->assertStatus(401);
    });
});
