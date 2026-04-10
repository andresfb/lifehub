<?php

namespace App\Domain\Dashboard\Http\Controllers\Api\V1;

use App\Domain\Dashboard\Actions\SearchProvidersAction;
use App\Domain\Dashboard\Http\Resources\SearchProviderResource;
use App\Domain\Dashboard\Models\SearchProvider;
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
