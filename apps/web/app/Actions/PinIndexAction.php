<?php

declare(strict_types=1);

namespace App\Actions;

use App\Dtos\PinIndexItem;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use App\Repository\Manifest\Services\ManifestService;
use Throwable;

final readonly class PinIndexAction
{
    public function __construct(
        private ManifestService $manifestService,
        private LoadUserPinsAction $pinsAction,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(int $userId): PinIndexItem
    {
        $bookmarks = $this->pinsAction->handle($userId);

        return new PinIndexItem(
            sections: $bookmarks->pluck('name', 'slug')
                ->unique()
                ->sortBy('order')
                ->toArray(),
            bookmarks: $bookmarks,
            modules: $this->manifestService->getUserNavigation($userId),
            canCreate: ManifestActionsLibrary::canAccess(
                $userId,
                ManifestModule::DASHBOARD,
                ManifestActionOwner::PINS,
                ManifestAction::SAVE,
                ManifestMethod::POST,
            ),
            canEdit: ManifestActionsLibrary::canAccess(
                $userId,
                ManifestModule::DASHBOARD,
                ManifestActionOwner::PINS,
                ManifestAction::UPDATE,
                ManifestMethod::PUT,
            ),
            canDelete: ManifestActionsLibrary::canAccess(
                $userId,
                ManifestModule::DASHBOARD,
                ManifestActionOwner::PINS,
                ManifestAction::DELETE,
                ManifestMethod::DELETE,
            ),
            createRouteName: 'dashboard.pins.create',
            updateRouteName: 'dashboard.pins.update',
            deleteRouteName: 'dashboard.pins.destroy',
        );
    }
}
