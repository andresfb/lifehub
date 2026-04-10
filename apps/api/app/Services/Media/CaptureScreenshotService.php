<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Domain\Bookmarks\Libraries\MediaNamesLibrary;
use App\Dtos\Media\PageScreenshotItem;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Spatie\Browsershot\Browsershot;
use Spatie\MediaLibrary\HasMedia;

final class CaptureScreenshotService
{
    /**
     * @throws Exception
     */
    public function execute(PageScreenshotItem $item, HasMedia $model): void
    {
        if (! Config::boolean('constants.browsershot_fallback')) {
            return;
        }

        if ($model->hasMedia($item->collection)) {
            return;
        }

        $directory = storage_path('app/private/screenshots');
        File::ensureDirectoryExists($directory);

        $tempPath = "{$directory}/{$item->modelId}.jpg";

        Browsershot::url($item->url)
            ->userAgent(Config::string('constants.crawler_agent'))
            ->timeout(Config::integer('constants.browsershot_timeout'))
            ->noSandbox()
            ->dismissDialogs()
            ->setOption('waitUntil', 'domcontentloaded')
            ->windowSize(1280, 800)
            ->save($tempPath);

        $model->addMedia($tempPath)
            ->toMediaCollection(MediaNamesLibrary::screenshot());
    }
}
