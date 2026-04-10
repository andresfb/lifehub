<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;

final class ReminderController extends ApiController
{
    public function __invoke($id): JsonResponse
    {
        return $this->noContent();
    }
}
