<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Services;

use App\Domain\Bookmarks\Dtos\BulkMarkerImportItem;
use App\Domain\Bookmarks\Enums\MarkerStatus;
use App\Domain\Bookmarks\Jobs\MarkerMutatorJob;
use App\Domain\Bookmarks\Models\Category;
use App\Domain\Bookmarks\Models\Marker;
use App\Dtos\AI\ApiErrorItem;
use App\Jobs\ProcessApiErrorsJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class BulkMarkerImportService
{
    private Collection $errors;

    public function __construct()
    {
        $this->errors = collect();
    }

    /**
     * @param  Collection<BulkMarkerImportItem>  $markers
     */
    public function execute(Collection $markers): void
    {
        $markers->each(function (BulkMarkerImportItem $item) {
            $markerId = 0;

            try {
                DB::transaction(function () use ($item, &$markerId): void {
                    if (Marker::found($item->url)) {
                        Log::notice(sprintf(
                            'Url %s already exists for User %s',
                            $item->url,
                            Auth::id())
                        );

                        return;
                    }

                    $url = trim($item->url);

                    $marker = new Marker;
                    $marker->user_id = Auth::id();
                    $marker->category_id = $this->getCategory($item->category);
                    $marker->hash = Marker::getHash($url);
                    $marker->url = $url;
                    $marker->title = trim($item->title);
                    $marker->status = MarkerStatus::from($item->status);
                    $marker->domain = $item->domain;
                    $marker->notes = $item->notes;
                    $marker->saveQuietly();

                    $marker = $marker->fresh();
                    $markerId = $marker->id;

                    if (filled($item->tags)) {
                        $marker->attachTags(
                            array_map(strtolower(...), $item->tags)
                        );
                    }
                });

                if (blank($markerId)) {
                    return;
                }

                MarkerMutatorJob::dispatch($markerId);
            } catch (Throwable $e) {
                Log::error($e->getMessage());

                $this->errors->push(ApiErrorItem::from([
                    'user_id' => Auth::id(),
                    'source_id' => (string) $item->id,
                    'type' => Marker::class,
                    'error' => $e->getMessage(),
                    'data' => [
                        'url' => $item->url,
                        'title' => $item->title,
                    ],
                ]));
            }
        });

        Cache::tags('markers')->flush();

        if ($this->errors->isEmpty()) {
            return;
        }

        ProcessApiErrorsJob::dispatch($this->errors);
    }

    private function getCategory(string $category): int
    {
        return Category::query()
            ->firstOrCreate([
                'title' => ucwords(mb_strtolower($category)),
                'user_id' => Auth::id(),
            ])->id;
    }
}
