<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Http\Controllers\Api\V1;

use App\Domain\Bookmarks\Actions\BulkMarkerImportAction;
use App\Domain\Bookmarks\Http\Requests\Api\V1\BulkMarkerImportRequest;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;

final class BulkMarkerImportController extends ApiController
{
    public function __invoke(BulkMarkerImportRequest $request, BulkMarkerImportAction $action): JsonResponse
    {
        $action->handle($request->markers);

        return $this->success(
            message: 'Markers queued for import successfully',
        );
    }
}
