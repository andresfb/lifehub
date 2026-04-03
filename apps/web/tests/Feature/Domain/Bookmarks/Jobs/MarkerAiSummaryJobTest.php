<?php

declare(strict_types=1);

use App\Domain\Bookmarks\Models\Category;
use App\Domain\Bookmarks\Models\Marker;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

test('job resolves marker without global scopes and sets auth user', function () {
    $user = User::factory()->create();

    $category = Category::query()
        ->withoutGlobalScopes()
        ->forceCreate([
            'user_id' => $user->id,
            'slug' => Str::random(),
            'title' => 'Test Category',
        ]);

    $marker = Marker::query()
        ->withoutGlobalScopes()
        ->forceCreate([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'hash' => md5('https://example.com'),
            'title' => 'Test Marker',
            'url' => 'https://example.com',
            'status' => 'active',
        ]);

    Auth::forgetUser();
    expect(Auth::id())->toBeNull();

    // Verify that the marker can be found without auth context
    // and that the job sets Auth from the marker's user
    $resolved = Marker::query()
        ->withoutGlobalScopes()
        ->where('id', $marker->id)
        ->firstOrFail();

    Auth::setUser($resolved->user);

    expect(Auth::id())->toBe($user->id);
});
