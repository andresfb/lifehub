<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\AccountUser;
use App\Models\Invitation;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen requires an invitation token', function () {
    $this->get(route('register'))
        ->assertForbidden();
});

test('registration screen returns 404 for invalid invitation token', function () {
    $this->get(route('register', ['invitation' => 'invalid-token']))
        ->assertNotFound();
});

test('registration screen renders with valid invitation', function () {
    $invitation = Invitation::factory()->create();

    $this->get(route('register', ['invitation' => $invitation->token]))
        ->assertOk();
});

test('registration screen returns 404 for expired invitation', function () {
    $invitation = Invitation::factory()->expired()->create();

    $this->get(route('register', ['invitation' => $invitation->token]))
        ->assertNotFound();
});

test('registration screen returns 404 for accepted invitation', function () {
    $invitation = Invitation::factory()->accepted()->create();

    $this->get(route('register', ['invitation' => $invitation->token]))
        ->assertNotFound();
});

test('new users can register with a valid invitation', function () {
    $invitation = Invitation::factory()->create(['email' => 'test@example.com']);

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'invitation' => $invitation->token,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $invitation->refresh();
    expect($invitation->accepted_at)
        ->not
        ->toBeNull()
        ->and(Account::query()->count())->toBe(1)
        ->and(AccountUser::query()->count())->toBe(1);

});

test('registration fails without invitation token', function () {
    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('invitation');
});

test('registration fails with expired invitation', function () {
    $invitation = Invitation::factory()->expired()->create(['email' => 'test@example.com']);

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'invitation' => $invitation->token,
    ])->assertSessionHasErrors('invitation');
});

test('registration fails with already accepted invitation', function () {
    $invitation = Invitation::factory()->accepted()->create(['email' => 'test@example.com']);

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'invitation' => $invitation->token,
    ])->assertSessionHasErrors('invitation');
});

test('registration fails with mismatched email', function () {
    $invitation = Invitation::factory()->create(['email' => 'invited@example.com']);

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'different@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'invitation' => $invitation->token,
    ])->assertSessionHasErrors('email');
});
