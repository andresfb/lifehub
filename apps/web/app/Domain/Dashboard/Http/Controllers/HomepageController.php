<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Http\Controllers;

use App\Domain\Dashboard\Actions\HomepageAction;
use App\Domain\Dashboard\Actions\SearchProvidersAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class HomepageController extends Controller
{
    public function __invoke(HomepageAction $homeAction, SearchProvidersAction $providersAction): Response
    {
        $userId = Auth::id();
        $sections = $homeAction->handle($userId);

        $providers = $providersAction->handle($userId);

        return $this->renderInertia(
            $userId,
            'Dashboard',
            compact('sections', 'providers')
        );
    }
}
