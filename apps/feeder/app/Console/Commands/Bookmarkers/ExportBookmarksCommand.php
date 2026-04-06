<?php

namespace App\Console\Commands\Bookmarkers;

use App\Models\Bookmarks\Marker;
use App\Models\ImportedMarker;
use App\Services\Bookmarks\ExportBookmarksService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\warning;

class ExportBookmarksCommand extends Command
{
    protected $signature = 'export:bookmarks';

    protected $description = 'Export all Bookmarks to LifeHub';

    public function handle(ExportBookmarksService $service): int
    {
        try {
            clear();
            intro('Exporting Markers');

            $maxRun = Config::integer('bookmarks.max_run');
            $service->setToScreen(true);

            Marker::query()
                ->with('section')
                ->with('tags')
                ->whereNotIn('id', ImportedMarker::imported())
                ->oldest()
                ->chunk(
                    $maxRun,
                    function (Collection $markers) use ($service, $maxRun): void {
                        $service->import($markers);

                        warning("LifeHub API only accepts $maxRun markers at a time. Waiting a few seconds...");
                        sleep(3);
                        $this->newLine();

                        dd('just those');
                    }
                );

            return self::SUCCESS;
        } catch (Exception $e) {
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }
}
