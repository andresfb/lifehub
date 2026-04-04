<?php

namespace App\Services\Api;

use App\Dtos\AI\ApiErrorItem;
use App\Models\ApiError;
use Illuminate\Support\Collection;

class ProcessApiErrorsService
{
    public function execute(Collection|ApiErrorItem $errors): void
    {
        if ($errors instanceof ApiErrorItem) {
            $errors = collect($errors);
        }

        $errors->each(function (ApiErrorItem $error) {
            ApiError::create($error->toArray());
        });
    }
}
