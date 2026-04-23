<?php

namespace App\Repository\Manifest\Services;

use App\Models\ApiManifest;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportApiCatalogService
{
    /**
     * @throws Throwable
     */
    public function execute(array $payload, int $userId): void
    {
        DB::transaction(static function() use ($payload, $userId) {
            ApiManifest::updateOrCreate([
                'user_id' => $userId
            ], [
                'version' => $payload['version'],
                'payload' => $payload['modules'],
            ]);
        });
    }
}
