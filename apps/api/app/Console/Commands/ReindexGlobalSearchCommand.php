<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SyncGlobalSearchChunksJob;
use App\Models\GlobalSearch;
use Illuminate\Console\Command;

final class ReindexGlobalSearchCommand extends Command
{
    protected $signature = 'search:reindex-global-search {--user_id= : Reindex records for one user only}';

    protected $description = 'Queue chunk and Meilisearch indexing for global search records';

    public function handle(): int
    {
        $userId = $this->option('user_id');
        $count = 0;

        GlobalSearch::query()
            ->when($userId !== null, fn ($query) => $query->where('user_id', $userId))
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($records) use (&$count): void {
                foreach ($records as $record) {
                    SyncGlobalSearchChunksJob::dispatch($record->id);
                    $count++;
                }
            });

        $this->info("Queued {$count} global search records for reindexing.");

        return self::SUCCESS;
    }
}
