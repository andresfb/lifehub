<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, Tag $tag): bool
    {
        return $user->is($tag->user);
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->is($tag->user);
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->is($tag->user);
    }

    public function restore(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user): bool
    {
        return $user->isAdmin();
    }
}
