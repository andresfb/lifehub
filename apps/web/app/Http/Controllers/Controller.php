<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApiManifestNavigationNode;
use App\Repository\Auth\Dtos\User;
use App\Repository\Manifest\Services\ManifestService;
use Illuminate\Support\Collection;
use Throwable;

abstract class Controller
{
    /**
     * @return Collection<ApiManifestNavigationNode>
     *
     * @throws Throwable
     */
    protected function getNavigation(User $user): Collection
    {
        return resolve(ManifestService::class)->getUserNavigation($user->id);
    }
}
