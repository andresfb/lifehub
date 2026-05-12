<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\SearchTagAction;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\SearchTagRequest;
use App\Http\Resources\Api\V1\TagResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

final class SearchTagController extends ApiController
{
    public function __invoke(SearchTagRequest $request, SearchTagAction $action): AnonymousResourceCollection
    {
        return TagResource::collection(
            $action->handle(
                (int) Auth::id(),
                $request->safe()->string('q')->value(),
            )
        );
    }
}
