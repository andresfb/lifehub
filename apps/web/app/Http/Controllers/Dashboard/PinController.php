<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\LoadUserPinsAction;
use App\Http\Controllers\Controller;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Laravel\Mcp\Exceptions\NotImplementedException;
use Throwable;

final class PinController extends Controller
{
    public function __construct(
        private readonly LoadUserPinsAction $pinsAction
    ) {}

    /**
     * @throws Throwable
     */
    public function index(): View
    {
        $userId = (int) Auth::id();

        $bookmarks = $this->pinsAction->handle($userId);
        $modules = $this->getNavigation($userId);

        return view(
            'dashboard.pins.index', [
                'sections' => $bookmarks->pluck('name', 'slug')
                    ->unique()
                    ->sortBy('order')
                    ->toArray(),
                'bookmarks' => $bookmarks,
                'modules' => $modules,
                'canCreate' => ManifestActionsLibrary::canAccess(
                    $userId,
                    ManifestModule::DASHBOARD,
                    ManifestActionOwner::PINS,
                    ManifestAction::SAVE,
                    ManifestMethod::POST,
                ),
                'canEdit' => ManifestActionsLibrary::canAccess(
                    $userId,
                    ManifestModule::DASHBOARD,
                    ManifestActionOwner::PINS,
                    ManifestAction::UPDATE,
                    ManifestMethod::PUT,
                ),
                'canDelete' => ManifestActionsLibrary::canAccess(
                    $userId,
                    ManifestModule::DASHBOARD,
                    ManifestActionOwner::PINS,
                    ManifestAction::DELETE,
                    ManifestMethod::DELETE,
                ),
            ]
        );
    }

    public function create(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }

    public function store(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }

    public function edit(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }

    public function update(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }

    public function destroy(): never
    {
        throw new NotImplementedException('Not implemented yet.');
    }
}
