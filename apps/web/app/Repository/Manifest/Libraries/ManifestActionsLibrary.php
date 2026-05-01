<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Libraries;

use App\Models\ApiManifestEndpoint;
use App\Repository\Manifest\Dtos\EndpointItem;
use App\Repository\Manifest\Enums\ManifestAction;
use App\Repository\Manifest\Enums\ManifestActionOwner;
use App\Repository\Manifest\Enums\ManifestEndpointType;
use App\Repository\Manifest\Enums\ManifestMethod;
use App\Repository\Manifest\Enums\ManifestModule;
use App\Repository\Manifest\Services\ApiManifestService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ManifestActionsLibrary
{
    public static function getEndpoint(
        int $userId,
        ManifestModule $module,
        ManifestActionOwner $owner,
        ManifestAction $action,
        ManifestMethod $method
    ): ?EndpointItem {
        try {
            $cached = self::getCached($userId, $module, $owner, $action, $method);
            if (blank($cached)) {
                resolve(ApiManifestService::class)->loadUserManifest($userId);
            }

            $cached = self::getCached($userId, $module, $owner, $action, $method);
            if (blank($cached)) {
                return null;
            }

            return EndpointItem::from($cached);
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return null;
        }
    }

    public static function canAccess(
        int $userId,
        ManifestModule $module,
        ManifestActionOwner $owner,
        ManifestAction $action,
        ManifestMethod $method
    ): bool {
        $endpoint = self::getEndpoint($userId, $module, $owner, $action, $method);

        return filled($endpoint);
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
                md5(sprintf(
                    "manifest-endpoint:%s:%s:%s:%s:%s",
                    $module->name,
                    $owner->name,
                    $action->name,
                    $method->name,
                    ManifestEndpointType::ACTION->name,
                )),
                now()->addDay(),
                fn (): ?array => ApiManifestEndpoint::query()
                    ->ofAction(
                        $userId,
                        $method->value,
                        $owner->value,
                        $action->value,
                        $module->value,
                        ManifestEndpointType::ACTION->value,
                    )->first()?->toArray()
            );
    }
}
