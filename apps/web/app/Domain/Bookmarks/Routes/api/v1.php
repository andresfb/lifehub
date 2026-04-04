<?php

declare(strict_types=1);

use App\Domain\Bookmarks\Http\Controllers\Api\V1\BulkMarkerImportController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'module.access:bookmarks,read',
])
    ->prefix('api/v1/bookmarks')
    ->group(function (): void {

        Route::get('/marker/{marker}', static function (Request $request) {
            echo 'TODO: Change this to a API Controller';
        })->name('api.v1.bookmarks.marker.show');

        Route::post('/marker/bulk/import', BulkMarkerImportController::class)
            ->middleware([
                'private.access',
                'module.access:bookmarks,write',
            ])
            ->name('api.v1.bookmarks.marker.bulk.import');

    });
