<?php

declare(strict_types=1);

namespace Modules\Core\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Dtos\AI\SearchHistoryItem;
use Modules\Core\Http\Requests\Api\V1\SearchHistoryCreateRequest;
use Modules\Core\Http\Requests\Api\V1\SearchHistoryFindRequest;
use Modules\Core\Http\Resources\Api\V1\SearchHistoryCollection;
use Modules\Core\Models\SearchHistory;
use Modules\Dashboard\Actions\SearchHistoryCreateAction;
use Throwable;

final class SearchHistoryController extends ApiController
{
    public function index(SearchHistoryFindRequest $request): SearchHistoryCollection
    {
        $term = SearchHistoryItem::from($request->validated());

        return new SearchHistoryCollection(
            SearchHistory::search($term->getQuery())
                ->where('user_id', Auth::id())
                ->where('module', $term->getModule())
                ->where('type', $term->getType())
                ->paginate(20)
        );
    }

    public function store(SearchHistoryCreateRequest $request, SearchHistoryCreateAction $action): JsonResponse
    {
        try {
            $hash = $action->handle(
                SearchHistoryItem::from($request->validated()),
            );
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }

        return $this->created(
            data: ['hash' => $hash],
            message: 'Search Term added successfully'
        );
    }

    public function destroy(SearchHistory $searchHistory): JsonResponse
    {
        $this->authorize('delete', $searchHistory);

        $searchHistory->delete();

        return $this->noContent();
    }
}
