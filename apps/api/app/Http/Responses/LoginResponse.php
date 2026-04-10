<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Authenticated successfully',
            'data' => new UserResource($request->user()),
        ]);
    }
}
