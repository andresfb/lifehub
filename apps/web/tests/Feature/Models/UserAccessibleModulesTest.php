<?php

declare(strict_types=1);

use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleStatus;
use App\Enums\ModuleVisibility;
use App\Models\Module;
use App\Models\User;

it('returns modules with at least READ access, visible, enabled, and active', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create(['status' => ModuleStatus::ACTIVE]);

    $user->modules()->attach($module, [
        'enabled' => true,
        'access_level' => ModuleAccessLevel::READ,
        'visibility' => ModuleVisibility::VISIBLE,
    ]);

    expect($user->accessibleModules()->pluck('id'))->toContain($module->id);
});

it('includes WRITE access level modules', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create(['status' => ModuleStatus::ACTIVE]);

    $user->modules()->attach($module, [
        'enabled' => true,
        'access_level' => ModuleAccessLevel::WRITE,
        'visibility' => ModuleVisibility::VISIBLE,
    ]);

    expect($user->accessibleModules()->pluck('id'))->toContain($module->id);
});

it('includes ADMIN access level modules', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create(['status' => ModuleStatus::ACTIVE]);

    $user->modules()->attach($module, [
        'enabled' => true,
        'access_level' => ModuleAccessLevel::ADMIN,
        'visibility' => ModuleVisibility::VISIBLE,
    ]);

    expect($user->accessibleModules()->pluck('id'))->toContain($module->id);
});

it('excludes modules with NONE access level', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create(['status' => ModuleStatus::ACTIVE]);

    $user->modules()->attach($module, [
        'enabled' => true,
        'access_level' => ModuleAccessLevel::NONE,
        'visibility' => ModuleVisibility::VISIBLE,
    ]);

    expect($user->accessibleModules()->pluck('id'))->not->toContain($module->id);
});

it('excludes modules with HIDDEN visibility', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create(['status' => ModuleStatus::ACTIVE]);

    $user->modules()->attach($module, [
        'enabled' => true,
        'access_level' => ModuleAccessLevel::READ,
        'visibility' => ModuleVisibility::HIDDEN,
    ]);

    expect($user->accessibleModules()->pluck('id'))->not->toContain($module->id);
});

it('excludes modules that are disabled', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create(['status' => ModuleStatus::ACTIVE]);

    $user->modules()->attach($module, [
        'enabled' => false,
        'access_level' => ModuleAccessLevel::READ,
        'visibility' => ModuleVisibility::VISIBLE,
    ]);

    expect($user->accessibleModules()->pluck('id'))->not->toContain($module->id);
});

it('excludes modules that are not ACTIVE', function () {
    $user = User::factory()->create();
    $module = Module::factory()->create(['status' => ModuleStatus::DISABLED]);

    $user->modules()->attach($module, [
        'enabled' => true,
        'access_level' => ModuleAccessLevel::READ,
        'visibility' => ModuleVisibility::VISIBLE,
    ]);

    expect($user->accessibleModules()->pluck('id'))->not->toContain($module->id);
});
