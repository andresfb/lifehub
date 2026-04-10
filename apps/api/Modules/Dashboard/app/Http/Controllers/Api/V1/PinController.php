<?php

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use Modules\Dashboard\Actions\PinsAction;
use Modules\Dashboard\Http\Resources\HomepageSectionResource;
use Modules\Dashboard\Models\HomepageItem;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class PinController extends ApiController
{
    public function index(PinsAction $homeAction): AnonymousResourceCollection
    {
        return HomepageSectionResource::collection(
            $homeAction->handle(Auth::id())
        );
    }

    public function store(Request $request)
    {
    }

    public function show(HomepageItem $bookmark)
    {
    }

    public function update(Request $request, HomepageItem $bookmark)
    {
    }

    public function destroy(HomepageItem $bookmark)
    {
    }
}
