<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Base\BaseUserCommand;
use App\Jobs\SyncGlobalSearchChunksJob;
use App\Models\GlobalSearch;

final class ReindexGlobalSearchCommand extends BaseUserCommand
{
    protected $signature = 'search:reindex-global-search {--user= : Reindex records for one user only}';

    protected $description = 'Queue chunk and Meilisearch indexing for global search records';

    public function handle(): int
    {
        $user = $this->loadUser();
        $count = 0;

        GlobalSearch::query()
            ->where('user_id', $user->id)
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
