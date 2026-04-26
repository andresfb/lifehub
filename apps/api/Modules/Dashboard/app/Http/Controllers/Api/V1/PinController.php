<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Exceptions\NotImplementedException;
use Modules\Dashboard\Actions\PinsAction;
use Modules\Dashboard\Http\Resources\Api\V1\HomepageSectionCollection;
use Modules\Dashboard\Models\HomepageItem;
use Throwable;

final class PinController extends ApiController
{
    /**
     * @throws Throwable
     */
    public function index(PinsAction $homeAction): HomepageSectionCollection
    {
        return $homeAction->handle(Auth::id());
    }

    public function store(Request $request): never
    {
        throw new NotImplementedException('store action not implemented');
    }

    public function show(HomepageItem $bookmark): never
    {
        throw new NotImplementedException('show action not implemented');
    }

    public function update(Request $request, HomepageItem $bookmark): never
    {
        throw new NotImplementedException('update action not implemented');
    }

    public function destroy(HomepageItem $bookmark): never
    {
        throw new NotImplementedException('destroy action not implemented');
    }
}
