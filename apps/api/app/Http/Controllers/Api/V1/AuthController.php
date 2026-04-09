<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController extends ApiController
{
    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }
}
