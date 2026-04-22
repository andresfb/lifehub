<?php

namespace App\Http\Controllers;

use App\Models\ApiCatalog;
use App\Repository\Auth\Dtos\User;
use App\Repository\Manifest\Services\ApiManifestService;
use Illuminate\Support\Facades\Concurrency;
use RuntimeException;
use Throwable;

abstract class Controller
{
    /**
     * @throws Throwable
     */
    protected function getCatalog(User $user): ApiCatalog
    {
        $catalog = ApiCatalog::getUserCatalog($user->id);
        if ($catalog instanceof ApiCatalog) {
            Concurrency::defer([
                fn () => ApiManifestService::checkVersion($user->id),
            ]);

            return $catalog;
        }

        resolve(ApiManifestService::class)->loadUserManifest($user->id);

        $catalog = ApiCatalog::getUserCatalog($user->id);
        if (! $catalog instanceof ApiCatalog) {
            throw new RuntimeException('No catalog found');
        }

        return $catalog;
    }
}
