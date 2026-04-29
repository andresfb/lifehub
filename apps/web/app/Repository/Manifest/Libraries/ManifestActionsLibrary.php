<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Libraries;

use App\Models\ApiManifestEndpoint;
use App\Repository\Manifest\Dtos\EndpointItem;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Services\ApiManifestService;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Throwable;

final class ManifestActionsLibrary
{
    /**
     * @throws Throwable
     */
    public static function getEndpoint(
        int $userId,
        ManifestModule $module,
        ManifestActionOwner $owner,
        ManifestAction $action,
        ManifestMethod $method
    ): EndpointItem {
        $cached = self::getCached($userId, $module, $owner, $action, $method);
        if (blank($cached)) {
            resolve(ApiManifestService::class)->loadUserManifest($userId);
        }

        $cached = self::getCached($userId, $module, $owner, $action, $method);
        if (blank($cached)) {
            throw new RuntimeException('Endpoint not found');
        }

        return EndpointItem::from($cached);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function getCached(
        int $userId,
        ManifestModule $module,
        ManifestActionOwner $owner,
        ManifestAction $action,
        ManifestMethod $method
    ): ?array {
        return Cache::tags(['manifest'])
            ->remember(
                md5("manifest-endpoint:{$module->name}:{$owner->name}:{$action->name}:{$method->name}"),
                now()->addDay(),
                fn (): ?array => ApiManifestEndpoint::query()
                    ->ofAction(
                        $userId,
                        $method->value,
                        $owner->value,
                        $action->value,
                        $module->value
                    )->first()?->toArray()
            );
    }
}
