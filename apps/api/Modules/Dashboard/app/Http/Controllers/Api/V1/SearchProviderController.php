<?php

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use Modules\Dashboard\Actions\SearchProvidersAction;
use Modules\Dashboard\Http\Resources\SearchProviderResource;
use Modules\Dashboard\Models\SearchProvider;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class SearchProviderController extends ApiController
{
    public function index(SearchProvidersAction $providersAction): AnonymousResourceCollection
    {
        return SearchProviderResource::collection(
            $providersAction->handle(Auth::id())
        );
    }

    public function store(Request $request)
    {
    }

    public function show(SearchProvider $provider)
    {
    }

    public function update(Request $request, SearchProvider $provider)
    {
    }

    public function destroy(SearchProvider $provider)
    {
    }
}
