<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Services;

use App\Models\ApiManifest;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ImportApiCatalogService
{
    /**
     * @throws Throwable
     */
    public function execute(array $payload, int $userId): void
    {
        DB::transaction(static function () use ($payload, $userId) {
            ApiManifest::query()
                ->updateOrCreate([
                    'user_id' => $userId,
                ], [
                    'version' => $payload['version'],
                    'payload' => $payload['modules'],
                ]);
        });
    }
}
