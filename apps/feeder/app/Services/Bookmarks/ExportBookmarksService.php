<?php

namespace App\Services\Bookmarks;

use App\Dtos\Bookmarks\MarkerRequest;
use App\Dtos\Bookmarks\MarkerItem;
use App\Dtos\LifeHubApiEndpoint;
use App\Libraries\LifeHubApiLibrary;
use App\Models\Bookmarks\Marker;
use App\Models\ImportedMarker;
use App\Traits\Screenable;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class ExportBookmarksService
{
    use Screenable;

    private readonly int $maxRun;

    public function __construct()
    {
        $this->maxRun = Config::integer('bookmarks.max_run');
    }

    /**
     * @throws Exception
     */
    public function execute(): void
    {
        $this->info('Starting export');

        $this->import(
            Marker::query()
                ->with('section')
                ->with('tags')
                ->whereNotIn('id', ImportedMarker::imported())
                ->oldest()
                ->limit($this->maxRun)
                ->get()
        );
    }

    /**
     * @param Collection<Marker> $markers
     * @throws Exception
     */
    public function import(Collection $markers): void
    {
        if ($markers->count() > $this->maxRun) {
            throw new RuntimeException("The LifeHub API requires at most {$this->maxRun} markers");
        }

        $this->notice("Exporting {$markers->count()} markers...");

        $toImport = $markers->map(function (Marker $marker) {
            $this->character('.');

            return new MarkerItem(
                id: $marker->id,
                category: $marker->section->title,
                status: $marker->status,
                title: $marker->title,
                url: str($marker->url)->rtrim('/')->toString(),
                domain: $marker->domain,
                notes: $marker->notes,
                tags: $marker->getTagList(),
            );
        });

        $this->line(2);
        $this->notice('Posting to the API');

        LifeHubApiLibrary::post(
            LifeHubApiEndpoint::BULK_MARKER_IMPORT,
            new MarkerRequest(
                markers: $toImport->toArray()
            )
        );

        ImportedMarker::saveImported($toImport);

        $this->notice('Done Exporting');
    }
}
