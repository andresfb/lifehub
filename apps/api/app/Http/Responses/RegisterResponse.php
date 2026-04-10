<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Symfony\Component\HttpFoundation\Response;

final class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): Response
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Registration successful',
            'data' => new UserResource($request->user()),
        ], Response::HTTP_CREATED);
    }
}
