<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Actions\SearchProviders;

use App\Repository\Dashboard\Dtos\SearchProviders\SearchProviderIndexItem;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Libraries\ManifestActionsLibrary;
use App\Repository\Manifest\Services\ManifestService;
use Throwable;

final readonly class SearchProviderIndexAction
{
    public function __construct(
        private ManifestService $manifestService,
        private LoadUserSearchProvidersAction $providersAction,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(int $userId): SearchProviderIndexItem
    {
        $searchEngines = $this->providersAction->handle($userId);

        return new SearchProviderIndexItem(
            searchEngines: $searchEngines,
            modules: $this->manifestService->getUserNavigation($userId),
            canCreate: ManifestActionsLibrary::canAccess(
                $userId,
                ManifestModule::DASHBOARD,
                ManifestActionOwner::SEARCH,
                ManifestAction::SAVE,
                ManifestMethod::POST,
            ),
            canEdit: ManifestActionsLibrary::canAccess(
                $userId,
                ManifestModule::DASHBOARD,
                ManifestActionOwner::SEARCH,
                ManifestAction::UPDATE,
                ManifestMethod::PUT,
            ),
            canDelete: ManifestActionsLibrary::canAccess(
                $userId,
                ManifestModule::DASHBOARD,
                ManifestActionOwner::SEARCH,
                ManifestAction::DELETE,
                ManifestMethod::DELETE,
            ),
            storeAction: route('dashboard.search-providers.store'),
            updateAction: route('dashboard.search-providers.update', ['provider' => '__PROVIDER__']),
            deleteRouteName: route('dashboard.search-providers.delete', ['provider' => '__PROVIDER__']),
        );
    }
}
