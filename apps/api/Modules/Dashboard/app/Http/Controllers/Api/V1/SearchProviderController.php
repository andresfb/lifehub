<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Exceptions\NotImplementedException;
use Modules\Dashboard\Actions\SearchProvidersAction;
use Modules\Dashboard\Http\Resources\Api\V1\SearchProviderCollection;
use Modules\Dashboard\Models\SearchProvider;

final class SearchProviderController extends ApiController
{
    public function index(SearchProvidersAction $providersAction): SearchProviderCollection
    {
        return $providersAction->handle(Auth::id());
    }

    public function store(Request $request): never
    {
        throw new NotImplementedException('store action not implemented');
    }

    public function show(SearchProvider $provider): never
    {
        throw new NotImplementedException('show action not implemented');
    }

    public function update(Request $request, SearchProvider $provider): never
    {
        throw new NotImplementedException('update action not implemented');
    }

    public function destroy(SearchProvider $provider): never
    {
        throw new NotImplementedException('destroy action not implemented');
    }
}
