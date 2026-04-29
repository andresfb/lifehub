<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\Manifest\Dtos\ModuleItem;
use App\Repository\Manifest\Services\ManifestService;
use Illuminate\Support\Collection;
use Throwable;

abstract class Controller
{
    /**
     * @return Collection<int, ModuleItem>
     *
     * @throws Throwable
     */
    protected function getNavigation(int $userId): Collection
    {
        return resolve(ManifestService::class)->getUserNavigation($userId);
    }
}
