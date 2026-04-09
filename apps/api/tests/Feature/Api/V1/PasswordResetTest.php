<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

describe('Forgot Password', function (): void {
    it('sends reset link successfully', function (): void {
        $user = User::factory()->create();

        $response = $this->postJson('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);
    });

    it('fails with non-existent email', function (): void {
        $response = $this->postJson('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
    });
});

describe('Reset Password', function (): void {
    it('resets password successfully with valid token', function (): void {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    });

    it('fails with invalid token', function (): void {
        $user = User::factory()->create();

        $response = $this->postJson('/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422);
    });

    it('fails with mismatched passwords', function (): void {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->postJson('/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422);
    });
});
