<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SearchTagAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\SearchTagRequest;
use App\Http\Resources\Api\V1\TagCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SearchTagController extends ApiController
{
    public function __invoke(SearchTagRequest $request, SearchTagAction $action): TagCollection|JsonResponse
    {
        $results = $action->handle(
            (int) Auth::id(),
            $request->safe()->string('q')->value(),
        );

        if ($results->isEmpty()) {
            return $this->notFound('No Tags Found');
        }

        return new TagCollection($results);
    }
}
