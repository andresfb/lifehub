<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Actions\Pins;

use App\Repository\Dashboard\Dtos\PinIndexItem;
use App\Repository\Dashboard\Enums\PinStatus;
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
        $bookmarks = $this->pinsAction->handle($userId, PinStatus::ALL);

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
            storeAction: route('dashboard.pins.store'),
            updateActionTemplate: route('dashboard.pins.update', ['pin' => '__PIN__']),
            deleteRouteName: 'dashboard.pins.destroy',
            searchTagsRouteName: 'search.tags',
        );
    }
}
