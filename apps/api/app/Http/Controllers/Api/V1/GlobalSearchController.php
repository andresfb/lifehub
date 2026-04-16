<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Search\GlobalSearchQueryServiceInterface;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\SearchGlobalSearchRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class GlobalSearchController extends ApiController
{
    public function __invoke(SearchGlobalSearchRequest $request, GlobalSearchQueryServiceInterface $search): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        return $this->success($search->search(
            user: $user,
            query: $request->string('q')->toString(),
            limit: $request->integer('limit', 20),
            filters: collect($validated)
                ->only(['module', 'entity_type', 'is_private', 'is_archived'])
                ->all(),
        ));
    }
}
