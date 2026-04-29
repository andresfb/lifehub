<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Modules\Core\Models\Reminder;

final class ReminderController extends ApiController
{
    public function __invoke(Reminder $reminder): JsonResponse
    {
        return $this->noContent();
    }
}
