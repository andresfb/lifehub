<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse
    {
        $userResource = new UserResource($request->user());

        return new JsonResponse([
            'success' => true,
            'message' => 'Authenticated successfully',
            'data' => $userResource->resolveResourceData($request),
        ]);
    }
}
