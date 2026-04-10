<?php

namespace Modules\Dashboard\Http\Controllers\Api\V1;

use Modules\Dashboard\Http\Resources\ModuleResource;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class MenuController extends ApiController
{
    public function __invoke(): AnonymousResourceCollection
    {
        return ModuleResource::collection(
            $this->menuAction->handle(Auth::id())
        );
    }
}
