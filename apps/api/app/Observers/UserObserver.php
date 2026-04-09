<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

final class UserObserver
{
    public function saved(User $user): void
    {
        Cache::tags('users')->flush();
    }
}
