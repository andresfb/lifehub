<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repository\Auth\Dtos\User;
use App\Repository\Manifest\Services\ManifestService;
use Throwable;

abstract class Controller
{
    /**
     * @throws Throwable
     */
    protected function getManifest(User $user): array
    {
        return resolve(ManifestService::class)->getForUser($user->id);
    }
}
