<?php

declare(strict_types=1);

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use _PHPStan_5a70c2d68\Nette\NotImplementedException;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Modules\Dashboard\Actions\PinsAction;
use Modules\Dashboard\Http\Resources\HomepageSectionResource;
use Modules\Dashboard\Models\HomepageItem;

final class PinController extends ApiController
{
    public function index(PinsAction $homeAction): AnonymousResourceCollection
    {
        return HomepageSectionResource::collection(
            $homeAction->handle(Auth::id())
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
