<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;

final class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Two-factor authentication successful',
            'data' => new UserResource($request->user()),
        ]);
    }
}
