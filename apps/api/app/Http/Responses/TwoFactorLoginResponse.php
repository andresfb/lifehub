<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;

final class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    public function toResponse($request): JsonResponse
    {
        $userResource = new UserResource($request->user());

        return new JsonResponse([
            'success' => true,
            'message' => 'Two-factor authentication successful',
            'data' => $userResource->resolveResourceData($request),
        ]);
    }
}
