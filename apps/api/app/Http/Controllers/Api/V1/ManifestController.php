<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Services\Manifest\ManifestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

final class ManifestController extends ApiController
{
    public function index(Request $request, ManifestService $manifestService): JsonResponse
    {
        return $this->success(
            $manifestService->buildForUser(
                $request->user()
            ),
        );
    }

    public function show(): JsonResponse
    {
        return $this->success([
            'version' => Config::string('settings.manifest.version'),
        ]);
    }
}
