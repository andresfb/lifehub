<?php

namespace App\Domain\Bookmarks\Http\Controllers\Api\V1;

use App\Domain\Bookmarks\Actions\BulkMarkerImportAction;
use App\Domain\Bookmarks\Http\Requests\Api\V1\BulkMarkerImportRequest;
use App\Http\Controllers\ApiController;

class BulkMarkerImportController extends ApiController
{
    public function __invoke(BulkMarkerImportRequest $request, BulkMarkerImportAction $action)
    {
        $action->handle($request->markers);

        return $this->noContent();
    }
}
