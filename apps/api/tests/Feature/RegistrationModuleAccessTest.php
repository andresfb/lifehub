<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(LazilyRefreshDatabase::class);

test('newly registered users receive core writer access', function (): void {
    Notification::fake();

    app(ModuleAccessService::class)->syncPermissions();

    $invitation = Invitation::factory()->create([
        'email' => 'new-user@example.com',
    ]);

    $this->postJson('/api/v1/register', [
        'name' => 'New User',
        'email' => 'new-user@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'invitation' => $invitation->token,
    ])->assertCreated();

    $user = User::query()
        ->where('email', 'new-user@example.com')
        ->firstOrFail();

    $moduleAccess = app(ModuleAccessService::class);

    expect($moduleAccess->canRead($user, 'core'))->toBeTrue()
        ->and($moduleAccess->canWrite($user, 'core'))->toBeTrue()
        ->and($user->hasRole($moduleAccess->readerRoleName('core')))->toBeFalse()
        ->and($user->hasRole($moduleAccess->writerRoleName('core')))->toBeTrue();

    Notification::assertSentTo($user, VerifyEmail::class);
});
