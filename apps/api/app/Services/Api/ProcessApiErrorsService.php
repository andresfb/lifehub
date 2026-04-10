<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Dtos\AI\ApiErrorItem;
use App\Models\ApiError;
use Illuminate\Support\Collection;

final class ProcessApiErrorsService
{
    public function execute(Collection|ApiErrorItem $errors): void
    {
        if ($errors instanceof ApiErrorItem) {
            $errors = collect($errors);
        }

        $errors->each(function (ApiErrorItem $error) {
            ApiError::query()->create($error->toArray());
        });
    }
}
