<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

final class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): JsonResponse
    {
        $userResource = new UserResource($request->user());

        return new JsonResponse([
            'success' => true,
            'message' => 'Registration successful',
            'data' => $userResource->resolveResourceData($request),
        ], Response::HTTP_CREATED);
    }
}
