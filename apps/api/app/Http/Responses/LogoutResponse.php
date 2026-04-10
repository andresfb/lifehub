<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Symfony\Component\HttpFoundation\Response;

final class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): Response
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
