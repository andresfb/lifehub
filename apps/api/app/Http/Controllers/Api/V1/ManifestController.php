<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Services\Manifest\ManifestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ManifestController extends ApiController
{
    public function __invoke(Request $request, ManifestService $manifestService): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $manifest = $manifestService->buildForUser($user);

        return response()->json($manifest->toArray());
    }
}
