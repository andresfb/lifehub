<?php

namespace App\Domain\Dashboard\Http\Controllers\Api\V1;

use App\Domain\Dashboard\Actions\HomepageAction;
use App\Domain\Dashboard\Actions\SearchProvidersAction;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HomepageController extends ApiController
{
    public function __invoke(HomepageAction $homeAction, SearchProvidersAction $providersAction): JsonResponse
    {
        $userId = Auth::id();

        $sections = $homeAction->handle($userId);
        $providers = $providersAction->handle($userId);
        $menus = $this->menuAction->handle($userId);

        return $this->success(
            compact('sections', 'providers', 'menus')
        );
    }
}
