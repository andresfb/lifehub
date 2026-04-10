<?php

namespace App\Domain\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;

class ReminderController extends ApiController
{
    public function __invoke($id)
    {
        return $this->noContent();
    }
}
