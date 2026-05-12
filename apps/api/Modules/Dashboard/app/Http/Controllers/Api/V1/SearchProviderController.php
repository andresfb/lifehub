<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\Dashboard\Actions\SearchProviderCreateAction;
use Modules\Dashboard\Actions\SearchProvidersAction;
use Modules\Dashboard\Actions\SearchProviderUpdateAction;
use Modules\Dashboard\Dtos\SearchProviderItem;
use Modules\Dashboard\Http\Requests\Api\V1\SearchProviderCreateRequest;
use Modules\Dashboard\Http\Requests\SearchProviderUpdateRequest;
use Modules\Dashboard\Http\Resources\Api\V1\SearchProviderResource;
use Modules\Dashboard\Models\SearchProvider;
use Throwable;

final class SearchProviderController extends ApiController
{
    public function index(SearchProvidersAction $providersAction): AnonymousResourceCollection
    {
        $this->authorize('viewAny', SearchProvider::class);

        return SearchProviderResource::collection(
            $providersAction->handle(Auth::id())
        );
    }

    public function store(SearchProviderCreateRequest $request, SearchProviderCreateAction $action): JsonResponse
    {
        try {
            $action->handle(
                Auth::id(),
                SearchProviderItem::from($request->validated())
            );
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }

        return $this->created(
            message: 'Search Provider created successfully'
        );
    }

    public function update(
        SearchProviderUpdateRequest $request,
        SearchProvider $provider,
        SearchProviderUpdateAction $action
    ): JsonResponse {
        try {
            $action->handle(
                provider: $provider,
                item: SearchProviderItem::from($request->validated()),
            );
        } catch (Throwable $e) {
            return $this->error($e->getMessage());
        }

        return $this->success(message: 'Pin updated successfully');
    }

    public function destroy(SearchProvider $provider): JsonResponse
    {
        $this->authorize('delete', $provider);

        $provider->delete();

        return $this->noContent();
    }
}
