<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use _PHPStan_5a70c2d68\Nette\NotImplementedException;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Dashboard\Actions\PinsAction;
use Modules\Dashboard\Http\Requests\Api\V1\HomeSectionRequest;
use Modules\Dashboard\Models\HomepageItem;
use Throwable;

final class PinController extends ApiController
{
    /**
     * @throws Throwable
     */
    public function index(HomeSectionRequest $request, PinsAction $homeAction): Response
    {
        return response(
            content: $homeAction->getJsonPayload(
                Auth::id(),
                $request->route()->getName(),
                $request->validated()
            ),
            status: 200,
            headers: ['Content-Type' => 'application/json']
        );
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
