<?php

declare(strict_types=1);

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

final class ModuleObserver
{
    public function saved(): void
    {
        Cache::tags('modules')->flush();
    }
}
